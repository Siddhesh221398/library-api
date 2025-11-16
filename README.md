# Library Management API Take Home Exercise
This exercise is designed to assess your ability to build a robust, production-ready RESTful API using Laravel. It covers Laravel fundamentals, API design, testing, performance, security, and more. The goal is to demonstrate your skills in building a secure, maintainable, and high-performance API application.

## Installation Guide
### 1. Clone repository
```bash
git clone https://github.com/Siddhesh221398/library-api
cd library-api
```
### 2. Install dependencies
```bash
composer install
```
### 3. Setup environment
Rename .env.example  to .env

### 4. Generate key
```bash
php artisan key:generate
```
### 5. Configure database (MySQL)
Update .env:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library
DB_USERNAME=root
DB_PASSWORD= 
```
### 6. Additional .env Settings (Cache)
```bash
CACHE_DRIVER=redis
```
### 7. Run migration + seeder
```bash
php artisan migrate --seed
```
### 8. Start local server
```bash
php artisan serve
```

## API Documentation (Swagger / OpenAPI)

This project includes interactive API documentation powered by Swagger, allowing you to explore, test, and understand all available API endpoints directly from your browser.

You can access the full API documentation at:
`http://127.0.0.1:8000/api/documentation`

- **Required .env Configuration:**    
  - For Swagger to work correctly, ensure the following lines exist in your .env file:     
    ```bash
    L5_SWAGGER_CONST_HOST=http://127.0.0.1:8000
    ```
    If your project is deployed on a server, update it accordingly:
    ```bash
    L5_SWAGGER_CONST_HOST=https://api.yourdomain.com
    ```


## Postman Collection & Environment
- **Location of Postman Files:**
  - The Postman testing files are stored inside:
    ```bash
    /database/postman_collection/
    ```
 - **How to Use These Files:**
   - Open Postman
   - Go to File → Import
   - Select both files from: 
     ```bash
        /database/postman_collection/
     ```
 - **Postman will import:**
   - The full API Collection
   - The Environment with variables


## How to Run Tests in Laravel
- **Execute the full test suite:**
   ```bash
    php artisan test
   ```
    Or directly using PHPUnit:
    ```bash
    vendor/bin/phpunit
   ```
 - **Run a Specific Test File:**   
     ```bash
        php artisan test --filter=AuthTest
        php artisan test --filter=BookTest
        php artisan test --filter=BorrowingTest
     ```

---
## For MySQL Challenge:

I have added all queries answer in the file, you can check it.

    library-api\database\query
 
