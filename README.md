StackZ Backend API

A robust, secure, and feature-rich quiz platform backend built with Symfony 5.12 and API Platform. StackZ provides a complete gamified learning experience with quizzes, missions, leaderboards, and virtual economy.
🚀 Features

    🔐 JWT Authentication - Secure token-based authentication system

    📚 Quiz Management - Create, manage, and take quizzes with multiple question types

    🎯 Daily Missions - Daily challenges with streak tracking and rewards

    🏆 Leaderboards - Global and category-based rankings

    💰 Virtual Economy - XP, currency, and reward system

    📊 User Progress - Comprehensive statistics and activity tracking

    👥 Multiplayer Ready - Infrastructure for future multiplayer features

    🛡️ Security - Built with security best practices and input validation

    📱 Mobile Ready - RESTful API optimized for mobile applications

🛠️ Tech Stack

    Framework: Symfony 5.12.0

    API: API Platform 3

    Authentication: JWT (LexikJWTAuthenticationBundle)

    Database: MySQL with Doctrine ORM

    Validation: Symfony Validator

    Serialization: Symfony Serializer

    Documentation: Swagger/OpenAPI 3

    Testing: PHPUnit, Postman

📋 Prerequisites

    PHP 8.1 or higher

    MySQL 8.0 or higher

    Composer

    Symfony CLI (recommended)

⚡ Quick Start
1. Clone the Repository
   - git clone https://github.com/your-username/stackz-backend.git
   - cd stackz-backend
2. Install Dependencies
   - composer install
3. Configure Environment
    # Copy environment file
    cp .env .env.local

    # Edit .env.local with your database credentials
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/stackz_db?serverVersion=8.0&charset=utf8mb4"
4. Generate JWT Keys
  # Generate JWT private key
openssl genpkey -out config/jwt/private.pem -algorithm RSA -pkeyopt rsa_keygen_bits:4096

# Generate JWT public key
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

# Set proper permissions
chmod 644 config/jwt/private.pem
chmod 644 config/jwt/public.pem

5. Set Up Database
   # Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Load sample data (optional)
php bin/console doctrine:fixtures:load

6. Start Development Server
   symfony server:start
# or
php bin/console server:start

**Your API is now running at http://localhost:8000**

📚 ** API Documentation**
**Interactive Documentation**

Visit the automatically generated API documentation:
http://localhost:8000/api/doc

**Postman Collection**
Import the Postman collection from:
/docs/postman/StackZ-API.postman_collection.json

🔐 **Authentication**

StackZ uses JWT (JSON Web Tokens) for authentication.
Register a New User:
POST /api/auth/register
Content-Type: application/json

{
  "email": "user@tony.com",
  "username": "username",
  "password": "password123"
}

Login:
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@tony.com",
  "password": "password123"
}

Response includes a JWT token:
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "user@tony.com",
    "username": "username"
  }
}

Using the JWT Token

Include the token in the Authorization header for protected endpoints:
Authorization: Bearer <jwt_token>

🎯 Core Endpoints
Quizzes
Method	Endpoint	Description	Auth Required
GET	/api/quizzes	Get all quizzes	✅
GET	/api/quizzes/{id}	Get specific quiz	✅
POST	/api/quiz-sessions	Submit quiz results	✅

User Management
Method	Endpoint	Description	Auth Required
GET	/api/auth/me	Get current user	✅
GET	/api/users/{id}	Get user profile	✅
PATCH	/api/users/{id}	Update user profile	✅

Daily Missions
Method	Endpoint	Description	Auth Required
GET	/api/missions/daily	Get daily mission	✅
POST	/api/missions/tasks/{id}/progress	Update task progress	✅
POST	/api/missions/tasks/{id}/claim	Claim task reward	✅

Leaderboards
Method	Endpoint	Description	Auth Required
GET	/api/leaderboard	Get leaderboard	✅

🗄️ Database Schema
Core Entities

    User - User accounts with profiles and statistics

    Quiz - Quiz metadata and configuration

    Question - Quiz questions with multiple choice answers

    QuizSession - Record of quiz attempts and scores

    DailyMission - Daily challenges and streak tracking

    MissionTask - Individual mission objectives

    ActivityLog - User activity history

    CurrencyTransaction - Virtual currency transactions

🧪 Testing
Run PHPUnit Tests
bash

php bin/phpunit

Test API Endpoints

Use the provided testing script:
bash

chmod +x test_api.sh
./test_api.sh

Manual Testing with cURL
bash

# Test registration
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@tony.com","username":"testuser","password":"password123"}'

# Test login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@tony.com","password":"password123"}'

🔧 Configuration
Environment Variables
Variable	Description	Default
DATABASE_URL	Database connection string	-
JWT_SECRET_KEY	Path to JWT private key	%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY	Path to JWT public key	%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE	JWT key passphrase	``
CORS_ALLOW_ORIGIN	CORS allowed origins	^https?://(localhost|127\.0\.0\.1)
Security Configuration

The application includes:

    JWT authentication

    Input validation

    SQL injection protection

    CORS configuration

    Rate limiting ready

    XSS protection
