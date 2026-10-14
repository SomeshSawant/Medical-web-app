# Medical web app — Medical Case Management System

A production-grade, secure web application built with **Laravel**, **MySQL**, and **Bootstrap 5** to manage surgical cases and implant device tracking from initial draft through clinical completion.

---

## 1. Project Overview & Clinical Context

In medical device logistics and orthopedic surgical workflows, accuracy, data integrity, and strict traceability are paramount. **Medical web app** provides clinical teams, sales representatives, and quality assurance auditors with:
- A real-time, database-driven clinical case management dashboard.
- Strict role-based access control (RBAC) separating administrative actions from read-only auditing.
- Search, filtering, and sorting across surgical implant procedures.
- A REST API authenticated via Laravel Sanctum personal access tokens.
- Complete regulatory audit trail metadata with patient de-identification (dummy references).

---

## 2. Technology Stack

- **Backend**: Laravel 11 / 12 (PHP 8.4)
- **Database**: MySQL (Default production/local setup) / SQLite (zero-config local dev & automated testing)
- **Frontend / UI**: Laravel Blade templates, **Bootstrap 5.3**, custom CSS, and **Bootstrap Icons 1.11**
- **Authentication**: Laravel Breeze (session-based web auth) + Laravel Sanctum (Bearer token REST API)
- **Authorization**: Laravel Policies (`CasePolicy`) and Gate authorization
- **Validation**: Form Requests (`StoreCaseRequest`, `UpdateCaseRequest`) with unique constraint validation
- **ORM & Data Layer**: Laravel Eloquent with eager loading relationships to prevent N+1 query overhead
- **Automated Testing**: PHPUnit feature test suite
- **Bonus Integrations**: Nager.Date Public Holidays API via Laravel HTTP Client with timeout & graceful fallback

---

## 3. User Roles & Permission Matrix

The application strictly enforces server-side authorization via `App\Policies\CasePolicy`. Permissions are never enforced solely through frontend UI hiding:

| Feature / Action | Admin | Viewer | Engineer *(Bonus)* | Enforcement Mechanism |
|---|:---:|:---:|:---:|---|
| **View Dashboard & Statistics** | Yes | Yes | Yes | `CasePolicy@viewAny` |
| **Browse, Search & Filter Cases** | Yes | Yes | Yes | `CasePolicy@viewAny` |
| **View Individual Case Details** | Yes | Yes | Yes | `CasePolicy@view` |
| **Create New Cases** | **Yes** | **No** (403) | **Yes** | `CasePolicy@create`, `StoreCaseRequest` |
| **Edit Existing Cases** | **Yes** | **No** (403) | **Yes** | `CasePolicy@update`, `UpdateCaseRequest` |
| **Change Case Status** | **Yes** | **No** (403) | **Yes** | `CasePolicy@update` |
| **Delete Cases** | **Yes** | **No** (403) | **No** (403) | `CasePolicy@delete` |
| **REST API Read Operations** | Yes | Yes | Yes | Sanctum Bearer Token |
| **REST API Write Operations** | **Yes** | **No** (403) | **Yes** | Sanctum Bearer Token + Policy |

---

## 4. Test Login Credentials (Local Dummy Data)

All test accounts are automatically provisioned when executing `php artisan db:seed`. No real patient health information (PHI) or real passwords are committed to source control.

| Role | Email Address | Password | Intended Evaluation Purpose |
|---|---|---|---|
| **Admin** | `admin@medicalapp.test` | `Admin@12345` | Full administrative control: Create, Edit, Status Transition, Delete |
| **Viewer** | `viewer@medicalapp.test` | `Viewer@12345` | Read-only auditor: Browse, Search, View Details; Blocked from write actions |
| **Engineer** *(Bonus)* | `engineer@medicalapp.test` | `Engineer@12345` | Technical specialist: Create, Edit, Transition Status; Blocked from Delete |

> **Quick-Fill Feature**: The login screen (`/login`) includes one-click buttons to automatically fill these test credentials for rapid evaluation.

---

## 5. Prerequisites

Before installing, ensure your environment has:
- **PHP** >= 8.2 (PHP 8.4 recommended) with extensions: `pdo`, `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `curl`, `fileinfo`.
- **Composer** (v2.x)
- **Node.js** >= 18 and **npm**
- **MySQL** (v8.0+ or MariaDB 10.4+) or **SQLite** (bundled with PHP)

---

## 6. Installation & Setup Instructions (Windows / Linux / macOS)

### Step 1: Clone or Navigate to Project Directory
```powershell
cd C:\Users\soham\.gemini\antigravity\scratch\medical-case-management
```

### Step 2: Install Dependencies
```bash
composer install
npm install
```

### Step 3: Environment Configuration
Copy the sample environment file and generate a unique application encryption key:
```bash
cp .env.example .env
php artisan key:generate
```

### Step 4: Configure the Database

#### Option A: MySQL (Default Standard Configuration)
1. Start your local MySQL service (e.g. via XAMPP, WampServer, Docker, or native Windows MySQL service).
2. Create the database in MySQL:
```sql
CREATE DATABASE medical_case_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
3. Update your `.env` database parameters:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medical_case_management
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

#### Option B: SQLite (Instant Zero-Config Alternative)
If MySQL is not installed on the reviewer machine, SQLite runs out of the box with zero external services required:
```dotenv
DB_CONNECTION=sqlite
```
*(The SQLite database file is located at `database/database.sqlite`.)*

### Step 5: Run Database Migrations & Seed Demo Records
Execute the safe migration command to build all database tables and seed both required user accounts plus 26+ fictional medical cases:
```bash
php artisan migrate:fresh --seed
```

### Step 6: Start the Development Server
You can launch the application server using standard Artisan:
```bash
php artisan serve
```
The application will be accessible at: **`http://localhost:8000`**

*(On Windows, pre-configured launcher scripts `serve.bat` or `serve.ps1` are also included in the repository root for one-click startup).*

---

## 7. REST API Documentation & Authorization Rules

The REST API is secured with **Laravel Sanctum Bearer Tokens** and produces standardized JSON envelopes.

### Standard Response Envelope Formats

#### Success Response (`200 OK` or `201 Created`):
```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": { ... }
}
```

#### Validation Error (`422 Unprocessable Entity`):
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "case_number": [
      "The case number has already been taken."
    ]
  }
}
```

#### Authorization Error (`403 Forbidden`):
```json
{
  "success": false,
  "message": "Unauthorized: Viewers cannot delete cases.",
  "data": null
}
```

### API Endpoints Table

| HTTP Method | Endpoint | Authorization | Description |
|:---:|---|:---:|---|
| `POST` | `/api/login` | Public | Authenticates credentials and returns a Sanctum Bearer token and user info |
| `POST` | `/api/logout` | Authenticated Bearer | Revokes the current token |
| `GET` | `/api/cases` | Admin & Viewer | Returns paginated list of cases (10/page), supports `search`, `status`, `priority` |
| `POST` | `/api/cases` | **Admin only** | Registers a new medical case (HTTP 201 on success, 422 on validation failure) |
| `GET` | `/api/cases/{id}` | Admin & Viewer | Retrieves full details of a specific case (HTTP 404 if not found) |
| `PUT` | `/api/cases/{id}` | **Admin only** | Updates case specifications; uniqueness check ignores current case ID |
| `DELETE` | `/api/cases/{id}` | **Admin only** | Permanently deletes a case (HTTP 403 for Viewer) |

### cURL Examples

#### 1. Login as Admin to Obtain Bearer Token:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@medicalapp.test","password":"Admin@12345"}'
```

#### 2. Retrieve Paginated Cases:
```bash
curl -X GET "http://localhost:8000/api/cases?search=Titanium&status=Planning" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <YOUR_TOKEN>"
```

#### 3. Create a Case (Admin):
```bash
curl -X POST http://localhost:8000/api/cases \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <ADMIN_TOKEN>" \
  -d '{
    "case_number": "CASE-9050",
    "patient_reference": "PAT-9912",
    "surgeon_name": "Dr. Eleanor Vance",
    "implant_type": "Titanium Hip Stem",
    "status": "Planning",
    "priority": "High",
    "surgery_date": "2026-11-20"
  }'
```

---

## 8. Importing the Postman Collection

An importable Postman collection is included in the project root:
- File: **`postman_collection.json`**

### Import Steps:
1. Open Postman.
2. Click **Import** in the upper left corner.
3. Drag and drop or browse to `postman_collection.json`.
4. The collection **Medical web app — Case Management API** will appear.
5. In the collection variables, verify `base_url` is set to `http://localhost:8000`.
6. Run the **Login (Admin)** request. A test script automatically captures the Sanctum Bearer token and stores it into the collection `{{token}}` variable for subsequent requests.

---

## 9. Automated Testing Suite

The application includes comprehensive feature tests verifying authentication, authorization, CRUD lifecycle, unique constraints, and the REST API.

### Run All Tests:
```bash
php artisan test
```

### Core Verification Coverage:
- `test_valid_login_succeeds`: Verifies successful session login.
- `test_invalid_login_is_rejected`: Rejects bad credentials with session errors.
- `test_guest_cannot_open_case_pages_or_apis`: Verifies unauthenticated redirection and HTTP 401 JSON API responses.
- `test_admin_can_create_a_case`: Verifies case creation and database persistence.
- `test_creating_a_case_with_a_duplicate_case_number_fails`: Validates HTTP 422 JSON validation errors and constraint integrity.
- `test_viewer_cannot_delete_a_case`: Verifies Viewer receives HTTP 403 Forbidden.
- `test_viewer_cannot_create_a_case`: Verifies Viewer creation attempt blocked with HTTP 403.
- `test_admin_can_delete_a_case`: Verifies Admin can delete cases.
- `test_engineer_can_create_and_edit_but_cannot_delete`: Verifies bonus Engineer role capabilities.
- `test_case_list_search_and_filters`: Validates search by surgeon and status filtering.
- `test_api_login_returns_token_and_user_profile`: Validates Sanctum token generation.
- `test_api_cases_list_returns_paginated_data`: Validates pagination structure (10 items/page).
- `test_api_admin_can_create_case`: Validates API case creation.
- `test_api_update_case`: Validates API case updates.
- `test_api_viewer_cannot_delete_case`: Validates API Viewer 403 response.

---

## 10. Security Notes & Best Practices

- **Role-Based Access Control**: Enforced using `App\Policies\CasePolicy` on every web and API route.
- **Server-Side Creator Attribution**: `created_by` is assigned directly from `$request->user()->id`; client-submitted creator IDs are never trusted.
- **Mass Assignment Protection**: Only audited fields are specified in `$fillable`.
- **SQL Injection Prevention**: All queries utilize Eloquent ORM parameterized bindings.
- **CSRF Protection**: Enabled on all web forms via `@csrf` Blade directives.
- **Password Security**: Passwords are saved using Laravel's native Bcrypt / Argon2id hashing algorithms.
- **Patient Privacy**: No real patient names or protected health information (PHI) are stored. De-identified dummy identifiers (e.g. `PAT-0042`) are strictly enforced.

---

## 11. Documentation Index

- [Database Architecture & ER Diagram](docs/database.md): Schema specifications, indexes, foreign keys, and Mermaid ER diagram.
- [Requirement Traceability Matrix](docs/traceability.md): Bidirectional mapping of requirements REQ-001 through REQ-010 to code and automated tests.
- [Cloud Deployment Blueprint](docs/deployment.md): Architecture for AWS EC2, Amazon RDS Multi-AZ MySQL, ALB with SSL, and KMS secret management.
