# Envato API Server

A secure, extensible Laravel API server that replaces the Envato Elements API, serving Template Kits from a local database.

## Features

- **RESTful API** for Template Kits management
- **Authentication** using Laravel Sanctum (token-based)
- **Rate Limiting** for DDoS protection (60 requests per minute)
- **Secure endpoints** with middleware protection
- **Extensible architecture** built on Laravel 11
- **Paginated responses** for efficient data retrieval
- **Filtering and search** capabilities

## Requirements

- PHP 8.3+
- Composer
- SQLite (or any database supported by Laravel)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/hunterd/envato-api-server.git
cd envato-api-server
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations and seed the database:
```bash
php artisan migrate
php artisan db:seed
```

6. Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Endpoints

### Authentication

#### Register a new user
```bash
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```bash
POST /api/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "password"
}
```

Response includes a token to use for authenticated requests.

#### Logout
```bash
POST /api/logout
Authorization: Bearer {your-token}
```

### Template Kits

#### List all template kits (Public)
```bash
GET /api/template-kits
```

Optional query parameters:
- `category` - Filter by category
- `is_active` - Filter by active status (true/false)
- `search` - Search by name
- `per_page` - Number of results per page (default: 15)

#### Get a single template kit (Public)
```bash
GET /api/template-kits/{id}
```

#### Create a template kit (Protected)
```bash
POST /api/template-kits
Authorization: Bearer {your-token}
Content-Type: application/json

{
  "name": "My Template Kit",
  "description": "Description here",
  "category": "Business",
  "author": "Author Name",
  "version": "1.0.0",
  "thumbnail": "https://example.com/image.jpg",
  "tags": ["tag1", "tag2"],
  "files": ["file1.html", "file2.css"],
  "price": 29.99,
  "is_active": true
}
```

#### Update a template kit (Protected)
```bash
PUT /api/template-kits/{id}
Authorization: Bearer {your-token}
Content-Type: application/json

{
  "name": "Updated Name",
  "price": 39.99
}
```

#### Delete a template kit (Protected)
```bash
DELETE /api/template-kits/{id}
Authorization: Bearer {your-token}
```

## Security Features

### Authentication
- Token-based authentication using Laravel Sanctum
- All write operations (POST, PUT, DELETE) require authentication
- Read operations (GET) are public

### Rate Limiting
- 60 requests per minute per IP address
- Prevents DDoS attacks
- Returns 429 status code when limit exceeded

### API Protection
- Input validation on all endpoints
- JSON error responses
- Secure token management

## Database Schema

### Template Kits Table
- `id` - Primary key
- `name` - Template name
- `description` - Template description
- `category` - Template category
- `author` - Template author
- `version` - Template version
- `thumbnail` - Thumbnail URL
- `tags` - JSON array of tags
- `files` - JSON array of file names
- `price` - Template price
- `is_active` - Active status
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

## Testing

Run the test suite:
```bash
php artisan test
```

## Development

### Linting
```bash
./vendor/bin/pint
```

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
