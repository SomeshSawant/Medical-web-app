# Medical Case Management System

A web application built in **Laravel** + **MySQL** / **Blade** to track medical device implant surgical cases from initial draft to clinical completion.

Designed for medical device manufacturers and clinical field specialists to streamline surgical case logistics, maintain strict role-based access control (RBAC), and preserve data integrity and traceability.

---

## Tech Stack

- **Backend Framework**: Laravel 13 (PHP 8.4)
- **Database**: MySQL (default) / SQLite (in-memory test suite)
- **Frontend / Templating**: Laravel Blade, Tailwind CSS, Alpine.js, Vite
- **API Authentication**: Laravel Sanctum (Bearer Tokens)
- **Web Authentication**: Laravel Breeze (Session-based RBAC)
- **Automated Testing**: PHPUnit (100% passing feature test suite)
- **External Integration**: Laravel HTTP Client calling Nager.Date Public Holidays API

---

## User Roles & Permission Matrix

The application enforces role-based authorization using a dedicated **Laravel Policy** (`CasePolicy`) and Form Requests.

| Role | View Cases | Create Cases | Edit Cases | Change Status | Delete Cases | Notes |
|---|:---:|:---:|:---:|:---:|:---:|---|
| **Admin** | Yes | Yes | Yes | Yes | **Yes** | Full clinical administration and deletion |
| **Viewer** | Yes | No | No | No | **No** | Read-only auditor; delete/create blocked with `403` |
| **Engineer** *(Bonus)* | Yes | Yes | Yes | Yes | **No** | Technical specialist; can update status, cannot delete |

---

## Test Login Credentials (Dummy Data Only)

All dummy users and sample cases are automatically created by `php artisan db:seed`.

| Role | Email | Password | Allowed Capabilities |
|---|---|---|---|
| **Admin** | `admin@example.com` | `password123` | View, Create, Edit, Status Transition, Delete |
| **Viewer** | `viewer@example.com` | `password123` | View cases, View single case details |
| **Engineer** *(Bonus)* | `engineer@example.com` | `password123` | View, Create, Edit, Status Transition (No Delete) |

> **Note**: No real patient or protected health information (PHI) is used in this repository. All clinical numbers and names are synthetic dummy test records.

---

## Features & Screens

1. **Authentication (Login / Logout)**:
   - Built on Laravel Breeze with secure session management.
   - Non-authenticated visitors are automatically blocked and redirected to `/login`.
   - Clear validation error messages displayed for incorrect credentials.

2. **Clinical Dashboard (`/dashboard`)**:
   - Status metric cards for: **Total Cases**, **Planning**, **In Review**, **Approved**, and **Completed** (plus **Draft** and **Cancelled**).
   - Recent cases table with direct links and status badges.
   - User role indicator pill customized to active session privileges.

3. **Case Directory (`/cases`)**:
   - Paginated table showing **10 cases per page**.
   - Live **Search** by Case Number or Surgeon Name.
   - **Filter** dropdown by Case Status.
   - *(Bonus)* **Filter** by Priority (`Low`, `Medium`, `High`) and **Column Sorting** (Case Number, Surgeon, Status, Priority, Surgery Date).
   - Dynamic action buttons: View (all), Edit (Admin & Engineer), Delete (Admin only with confirm prompt).

4. **Reusable Create & Edit Case Form (`/cases/create`, `/cases/{id}/edit`)**:
   - Reused Blade form partial (`resources/views/cases/_form.blade.php`).
   - Field-level validation error messages displayed alongside each input.
   - Pre-populated fields on edit with unique rule ignoring current case.
   - *(Bonus)* Live integration displaying upcoming public holidays next to the Surgery Date field via Nager.Date API.

5. **Case Details Screen (`/cases/{id}`)**:
   - Full specification overview: Case Number, Patient Reference dummy code, Surgeon Name, Implant Device, Status, Priority, Surgery Date.
   - Audit trail metadata: Created by (Name, Email, Role) and timestamp logs.
   - Server-enforced Delete button strictly restricted to Admin users (returns `403 Forbidden` if unauthorized user attempts request).

---

## REST API Documentation

All API endpoints return JSON in a consistent schema and use standard HTTP status codes:
- `200 OK`: Request succeeded.
- `201 Created`: Resource successfully created.
- `401 Unauthorized`: Missing or invalid Sanctum token.
- `403 Forbidden`: Authenticated user lacks role permissions.
- `404 Not Found`: Case not found.
- `422 Unprocessable Entity`: Field-level validation failed.

### Standard Response Envelope
```json
{
  "success": true,
  "message": "Medical cases retrieved successfully.",
  "data": { ... }
}
```

### Endpoints Table

| Method | Endpoint | Purpose | Access Control | Headers Required |
|---|---|---|---|---|
| `POST` | `/api/login` | Authenticate and obtain Sanctum Bearer token | Anyone | `Content-Type: application/json` |
| `POST` | `/api/logout` | Revoke active API token | Logged-in | `Authorization: Bearer <token>` |
| `GET` | `/api/user` | Get profile of logged-in user | Logged-in | `Authorization: Bearer <token>` |
| `GET` | `/api/cases` | List cases (paginated; supports `search`, `status`, `priority`) | Admin, Viewer, Engineer | `Authorization: Bearer <token>` |
| `POST` | `/api/cases` | Create a new medical case | Admin, Engineer | `Authorization: Bearer <token>` |
| `GET` | `/api/cases/{id}` | Retrieve details for a single case | Admin, Viewer, Engineer | `Authorization: Bearer <token>` |
| `PUT` | `/api/cases/{id}` | Update an existing medical case | Admin, Engineer | `Authorization: Bearer <token>` |
| `DELETE` | `/api/cases/{id}` | Permanently delete a case | **Admin only** | `Authorization: Bearer <token>` |

> **Postman Collection**: A complete, ready-to-import Postman collection is exported at [`postman_collection.json`](postman_collection.json).

---

## Setup & Installation Guide

Follow these steps to run the application locally:

### 1. Clone the repository
```bash
git clone <repository-url>
cd medical-case-management
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Environment configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env` (MySQL or SQLite):
```env
# MySQL configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_case_management
DB_USERNAME=root
DB_PASSWORD=

# Or quick local SQLite:
# DB_CONNECTION=sqlite
```

### 4. Run database migrations & seeders
```bash
php artisan migrate:fresh --seed
```
*This creates the schema and seeds 3 user accounts and 26 dummy medical cases.*

### 5. Compile frontend assets (Optional if using committed build)
The production assets are already pre-compiled in `public/build/`. If you want to re-compile or run development watch:
```bash
npm install
npm run build
```

### 6. Start the local server
```bash
php artisan serve
```
Open **[http://localhost:8000](http://localhost:8000)** in your browser and sign in using `admin@example.com` / `password123`.

---

## Running Automated Tests

Run the complete feature test suite using PHPUnit / Artisan:

```bash
php artisan test
```

### Test Coverage Highlights
All 6 mandatory feature requirements and bonus scenarios are verified:
1. `test_valid_login_succeeds` — Valid credentials authenticate and redirect to dashboard.
2. `test_invalid_login_is_rejected` — Invalid credentials rejected with session error.
3. `test_guest_cannot_open_case_pages_or_apis` — Unauthenticated guests redirected on web or returned `401` on API.
4. `test_admin_can_create_a_case` — Admin creates case with audit attribution.
5. `test_creating_a_case_with_a_duplicate_case_number_fails` — Duplicate Case Number blocked with `422` validation error.
6. `test_viewer_cannot_delete_a_case` — Viewer blocked from deletion with `403 Forbidden` on both Web and API.
7. `test_viewer_cannot_create_a_case` — Viewer blocked from creating cases (`403`).
8. `test_engineer_can_create_and_edit_but_cannot_delete` — Bonus Engineer role verified.
9. `test_case_list_search_and_filters` — Case directory search and status filtering verified.
10. `MedicalCaseApiTest` — Complete REST API test coverage for login, token revoke, pagination, and CRUD.

---

## Bonus Implementations (Extra Credit)

- [x] **Third Role (Engineer)**: Implemented `engineer` role capable of creating, editing, and advancing case statuses, while strictly barred from case deletion.
- [x] **Case List Sorting & Priority Filter**: Case directory supports sorting by any table header and filtering by Priority.
- [x] **Public Holiday API Integration**: Integrated Nager.Date API (`https://date.nager.at`) via Laravel HTTP Client with a 3-second timeout and graceful fault handling.
- [x] **AWS Deployment Architecture Documentation**: Half-page production cloud architecture guide at [`docs/deployment.md`](docs/deployment.md) detailing EC2, Multi-AZ RDS MySQL, S3, Security Groups, Secrets Manager, and ACM/HTTPS.
- [x] **Medical Software Traceability Matrix**: Located at [`docs/traceability.md`](docs/traceability.md) linking requirements to code and test verifications.
- [x] **Database Schema Documentation**: Detailed data dictionary and Mermaid ER diagram at [`docs/database.md`](docs/database.md).

---

## Assumptions & Known Limitations

1. **Patient Data Privacy**: Per assignment specifications, patient references use synthetic alphanumeric codes (e.g. `PAT-NX401`) rather than real patient names or health identifiers.
2. **Public Holiday API Availability**: If the external Nager.Date API is offline or times out (>3s), the application catches the exception gracefully, logs the event, and renders the case form without blocking clinical workflow.
3. **Database Portability**: The migration uses standard Eloquent Schema builder syntax compatible with MySQL in production and in-memory SQLite for high-speed automated testing.
