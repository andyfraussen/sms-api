# Laravel API

This project is a Laravel-based API that handles operations related to schools, students, attendances, and assessments. It is secured using Sanctum for API authentication and supports versioning under the `/api/v1` prefix.

## Table of Contents

- [Getting Started](#getting-started)
- [Authentication](#authentication)
- [API Endpoints](#api-endpoints)
  - [Schools](#schools)
  - [Students](#students)
  - [Attendances](#attendances)
  - [Assessments](#assessments)
- [Validation](#validation)
- [Running the Application](#running-the-application)
- [Additional Information](#additional-information)

## Getting Started

1. **Clone the repository:**
   ```bash
   git clone <REPO_URL>
   cd sms-api
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure your environment:**
   - Copy the `.env.example` file to `.env` and update your environment variables (database connection, mail settings, etc.).
   - Generate an application key:
     ```bash
     php artisan key:generate
     ```

## Authentication

- The API uses Laravel Sanctum to secure routes.
- Every endpoint under `/api/v1` requires authentication via Sanctum middleware.
- Clients must include an API token in the request header (`Authorization: Bearer <TOKEN>`).

## API Endpoints

All API endpoints are grouped under the `/api/v1` prefix.

### Schools

Resource endpoints for school entities are available:

- **GET** `/api/v1/schools` - List all schools.
- **POST** `/api/v1/schools` - Create a new school.
- **GET** `/api/v1/schools/{id}` - Get details of a specific school.
- **PUT/PATCH** `/api/v1/schools/{id}` - Update a school.
- **DELETE** `/api/v1/schools/{id}` - Delete a school.

### Students

Resource endpoints for student entities include additional routes:

- **GET** `/api/v1/students` - List all students.
- **GET** `/api/v1/students/trashed` - List all soft-deleted (trashed) students.
- **POST** `/api/v1/students` - Create a new student.
- **GET** `/api/v1/students/{id}` - Get details of a specific student.
- **PUT/PATCH** `/api/v1/students/{id}` - Update a student.
- **DELETE** `/api/v1/students/{id}` - Soft-delete a student.
- **POST** `/api/v1/students/{id}/restore` - Restore a soft-deleted student.
- **DELETE** `/api/v1/students/{id}/force` - Permanently delete a student.

### Attendances

Resource endpoints for manage attendance records:

- **GET** `/api/v1/attendances` - List all attendance records.
- **POST** `/api/v1/attendances` - Create a new attendance record.
- **GET** `/api/v1/attendances/{id}` - Get a specific attendance record.
- **PUT/PATCH** `/api/v1/attendances/{id}` - Update an attendance record.
- **DELETE** `/api/v1/attendances/{id}` - Delete an attendance record.

### Assessments

The API provides endpoints for handling assessments with full CRUD support.

- **GET** `/api/v1/assessments` - List all assessments records.
- **POST** `/api/v1/assessments` - Create a new assessments record.
- **GET** `/api/v1/assessments/{id}` - Get a specific assessments record.
- **PUT/PATCH** `/api/v1/assessments/{id}` - Update an assessments record.
- **DELETE** `/api/v1/assessments/{id}` - Delete an assessments record.

## Validation

This API uses dedicated Form Request classes for validating data:

- **StoreAssessmentRequest** - Validates the data required for creating a new assessment.
- **UpdateAssessmentRequest** - Validates the data for updating an existing assessment. Fields are optional and only validated when provided.

## Running the Application

To run the application locally:

1. **Start the development server:**
   ```bash
   php artisan serve
   ```

2. **Handle database migrations:**
   ```bash
   php artisan migrate
   ```

3. **Queue Connection:**
   The application uses the database queue driver. To process queued jobs, run:
   ```bash
   php artisan queue:work
   ```

## Additional Information

- **Testing:**  
  The project uses the Pest PHP testing framework. You can run tests using:
  ```bash
  php artisan test
  ```

- **Additional Packages:**  
  The application includes several third-party packages such as Guzzle for HTTP requests, Faker for generating test data, Monolog for logging, and more. Refer to the `composer.json` for a complete list of dependencies.

- **API Versioning:**  
  Endpoints are versioned under `v1`. Plan for future backward-compatible changes by maintaining versioned routes.

For any questions or issues, please refer to the project's documentation or open an issue on the repository.
