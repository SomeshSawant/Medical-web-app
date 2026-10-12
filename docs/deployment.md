# AWS Cloud Deployment Architecture (Production Blueprint)

This document details how the Medical Case Management application is hosted in production on Amazon Web Services (AWS) adhering to HIPAA-compatible high availability, data security, and encryption standards.

---

## 1. Architectural Topology Overview

```
 [Internet Users / Clients]
              │
              ▼ HTTPS (TCP 443)
   [Route 53 DNS + AWS WAF]
              │
              ▼
  [Application Load Balancer (ALB)]
       (ACM TLS Certificate)
        │                 │
        │ HTTP (TCP 80)   │
        ▼                 ▼
 ┌────────────────────────────────────────────────────────┐
 │ VPC (10.0.0.0/16) — Public Subnets                      │
 └────────────────────────────────────────────────────────┘
        │
        ▼ Target Group
 ┌────────────────────────────────────────────────────────┐
 │ Private App Subnets (10.0.1.0/24, 10.0.2.0/24)         │
 │                                                        │
 │  ┌──────────────────────┐    ┌──────────────────────┐  │
 │  │ EC2 (PHP 8.4 + Nginx)│    │ EC2 (PHP 8.4 + Nginx)│  │
 │  │ Auto Scaling Group   │    │ Auto Scaling Group   │  │
 │  └──────────┬───────────┘    └──────────┬───────────┘  │
 └─────────────┼───────────────────────────┼──────────────┘
               │                           │
               ▼ TCP 3306                  ▼ HTTPS
 ┌─────────────────────────────┐  ┌───────────────────────┐
 │ Private Data Subnets        │  │ Amazon S3             │
 │                             │  │                       │
 │  ┌────────────────────────┐ │  │ • Clinical assets     │
 │  │ Amazon RDS Multi-AZ    │ │  │ • Server-side KMS SSE │
 │  │ MySQL 8.4 Database     │ │  │ • Private bucket      │
 │  └────────────────────────┘ │  └───────────────────────┘
 └─────────────────────────────┘
```

---

## 2. Component Specifications

### A. Compute Layer (EC2 / Auto Scaling)
- **Instance Sizing**: `t4g.small` or `c7g.large` AWS Graviton3 compute instances running Amazon Linux 2023 or Ubuntu 24.04 LTS.
- **Web Stack**: Nginx with PHP 8.4-FPM running behind `systemd` process managers. OPcache enabled with preloading.
- **Auto Scaling Group (ASG)**: Spanning across 2 Availability Zones (`us-east-1a` and `us-east-1b`) with a minimum of 2 instances and CPU/latency target tracking policies.

### B. Database Layer (Amazon RDS for MySQL)
- **Engine**: MySQL 8.4 LTS in a **Multi-AZ Deployment** for automated synchronous failover and continuous zero-downtime replication.
- **Storage**: General Purpose SSD (`gp3`) with automated daily snapshots retained for 35 days.
- **Data Protection**: Encryption at rest using **AWS KMS** customer-managed keys (`aws/rds`). Automated automated point-in-time recovery (PITR).

### C. File Storage (Amazon S3)
- **Storage**: Private Amazon S3 bucket configured via Laravel's `league/flysystem-aws-s3-v3` driver (`config/filesystems.php`).
- **Access Policy**: Enforce `Block Public Access` (all 4 settings enabled). Files are served through time-limited pre-signed URLs or CloudFront Origin Access Control (OAC).
- **Encryption**: SSE-KMS (Server-Side Encryption with AWS Key Management Service).

### D. Security Groups & Network Isolation (Least Privilege)
- **ALB Security Group (`sg-alb`)**:
  - Inbound: `TCP 443` (HTTPS) and `TCP 80` (HTTP redirected to HTTPS) from `0.0.0.0/0`.
  - Outbound: `TCP 80` restricted to `sg-ec2`.
- **EC2 Security Group (`sg-ec2`)**:
  - Inbound: `TCP 80` from `sg-alb` only. No direct public inbound ingress.
  - Inbound SSH: Optional `TCP 22` via AWS Systems Manager (SSM) Session Manager (no open bastion ports).
  - Outbound: `TCP 3306` to `sg-rds`, and `TCP 443` to AWS endpoints/internet via NAT Gateway.
- **RDS Security Group (`sg-rds`)**:
  - Inbound: `TCP 3306` strictly from `sg-ec2`. Never accessible from the public internet.

### E. Environment Secrets & Configuration Management
- **AWS Secrets Manager / SSM Parameter Store**:
  - Sensitive credentials (`APP_KEY`, `DB_PASSWORD`, `SEED_ADMIN_PASSWORD`) are never committed to Git or stored in raw `.env` files on disk.
  - At EC2 boot or container startup, deployment user-data fetches parameters using IAM roles (`arn:aws:iam::role/MedicalCaseEC2Role`) and injects them as runtime environment variables.

### F. Domain & SSL/TLS Encryption
- **Domain & DNS**: Amazon Route 53 with an Alias record routing traffic to the ALB.
- **Certificate**: Free automated SSL/TLS certificate managed by **AWS Certificate Manager (ACM)** with automatic DNS renewal.
- **Enforcement**: ALB HTTP listener automatically issues `301 Moved Permanently` redirecting all HTTP requests to `HTTPS://443` with TLS 1.3 enforced.
