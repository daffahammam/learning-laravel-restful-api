# Learning Laravel RESTful API with OpenAPI

This project is a learning study on how to build a RESTful API using **Laravel** and document it using **OpenAPI (Swagger)**. It includes API implementations for basic user management, contact handling, and address data management.

## Features

- RESTful API built with Laravel
- OpenAPI (Swagger) documentation
- Organized API specification files:
  - `user-api.json`
  - `contact-api.json`
  - `address-api.json`
- Basic CRUD operations for:
  - Users
  - Contacts
  - Addresses

## Tech Stack

- **Laravel 10**
- **OpenAPI 3.0** for API specification
- **Laravel Sanctum** (if authentication is used)
- JSON-based API specs for documentation and testing

## Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- Laravel CLI
- MySQL
- Node.js & npm (optional for frontend or Swagger UI)

### Installation

```bash
# Clone the repository
git clone https://github.com/daffahammam/learning-laravel-restful-api.git

cd learning-laravel-restful-api

# Install dependencies
composer install

# Copy .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Set up database in .env, then run:
php artisan migrate

# Run the development server
php artisan serve
