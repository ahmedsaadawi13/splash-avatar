# SplashAvatar

**AI Avatar Generator SaaS - Multi-Tenant Credits-Based Platform**

SplashAvatar is a complete multi-tenant SaaS application built in PHP and MySQL that allows users and companies to upload face photos, select avatar styles (anime, 3D, cartoon, pixel, cyberpunk, etc.), and generate professional avatar packs. The platform features a credits-based subscription system, REST API for programmatic access, custom branding, and comprehensive multi-tenant isolation.

---

## Features

### Core Features
- **Multiple Avatar Styles**: Anime Neon, 3D Pixar-like, Cartoon Classic, Pixel Retro, Cyberpunk Neon, Watercolor Art, and more
- **Batch Generation**: Upload multiple face photos and generate avatar packs with multiple styles simultaneously
- **Credits-Based System**: Flexible pricing with monthly credits allocation per subscription plan
- **Avatar Packs & Galleries**: Organized collections of generated avatars with download capabilities
- **Face Photo Management**: Upload, validate, and manage face photos with simulated face detection

### Multi-Tenant Architecture
- **Complete Tenant Isolation**: All business data is strictly isolated per tenant
- **Role-Based Access Control**: Platform Admin, Tenant Admin, Designer, and End User roles
- **Custom Branding**: Logo, colors, and watermark customization per tenant
- **Subscription Plans**: Free, Starter, Pro, and Enterprise plans with different credit allocations

### REST API
- **Programmatic Access**: Full API for avatar generation integration
- **API Key Authentication**: Secure API keys with rate limiting
- **Endpoints**:
  - `POST /api/v1/avatar-jobs/create` - Create new avatar generation job
  - `GET /api/v1/avatar-jobs/{id}` - Get job status
  - `GET /api/v1/avatar-jobs/{id}/avatars` - List generated avatars
  - `GET /api/v1/avatar-packs/{id}` - Get avatar pack with download links

### Security
- Password hashing with `password_hash()` and `password_verify()`
- Session-based authentication with session regeneration on login
- CSRF token validation for all non-GET forms
- PDO prepared statements (no SQL injection vulnerabilities)
- Strict tenant isolation across all resources
- Input validation and sanitization
- File upload validation (type, size, content)

---

## Tech Stack

- **Backend**: PHP 7.0+ (compatible with PHP 7.0 through 8.x)
- **Database**: MySQL 5.7+ with InnoDB engine
- **Architecture**: Custom lightweight MVC (no Laravel, no Symfony)
- **Frontend**: Vanilla JavaScript + HTML + CSS
- **Extensions Required**:
  - `pdo_mysql`
  - `mbstring`
  - `json`
  - `openssl`
  - `fileinfo`
  - `gd` (for image processing)

---

## Installation

### Requirements

- PHP 7.0 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- PHP extensions: `pdo_mysql`, `mbstring`, `json`, `openssl`, `fileinfo`, `gd`

### Step 1: Clone the Repository

```bash
git clone https://github.com/ahmedsaadawi13/splash-avatar.git
cd splash-avatar
```

### Step 2: Configure Environment

```bash
cp .env.example .env
```

Edit `.env` and configure your database credentials:

```env
APP_NAME=SplashAvatar
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_HOST=localhost
DB_NAME=splashavatar
DB_USER=root
DB_PASS=your_password
```

### Step 3: Create Database & Import Schema

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE splashavatar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schema and seed data
mysql -u root -p splashavatar < database.sql
```

### Step 4: Set Permissions

```bash
chmod -R 755 storage/
chmod -R 755 storage/uploads/
chmod -R 755 storage/logs/
```

### Step 5: Configure Web Server

#### Apache

Create virtual host or update `.htaccess` (already included in `/public`):

```apache
<VirtualHost *:80>
    ServerName splashavatar.local
    DocumentRoot /path/to/splash-avatar/public

    <Directory /path/to/splash-avatar/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/splashavatar_error.log
    CustomLog ${APACHE_LOG_DIR}/splashavatar_access.log combined
</VirtualHost>
```

Enable mod_rewrite:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx

```nginx
server {
    listen 80;
    server_name splashavatar.local;
    root /path/to/splash-avatar/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### Step 6: Access the Application

Navigate to `http://localhost` or your configured domain.

---

## Default Demo Credentials

### Platform Admin
- **Email**: `admin@splashavatar.com`
- **Password**: `admin123`
- **Access**: Full platform administration

### Tenant Admin (Demo Company)
- **Email**: `demo@demo.com`
- **Password**: `demo123`
- **Access**: Manage tenant settings, branding, billing, users

### End User (Demo Company)
- **Email**: `user@demo.com`
- **Password**: `user123`
- **Access**: Upload faces, create jobs, download avatars

---

## Usage Guide

### For End Users

1. **Login** to your account
2. **Upload Face Photos**:
   - Navigate to "Faces" → "Upload"
   - Select one or more JPG/PNG images (max 10MB each)
   - Photos are automatically validated for face detection
3. **Create Avatar Job**:
   - Navigate to "Jobs" → "Create New Job"
   - Select validated face photos
   - Choose avatar styles
   - Review credits cost
   - Click "Create Job"
4. **Run Job**:
   - After creating, click "Run Job Now"
   - Pipeline generates avatars for each (face × style) combination
5. **View & Download**:
   - Navigate to "Packs" to see generated avatar packs
   - Download individual avatars or entire packs

### For Tenant Admins

1. **Manage Branding**:
   - Navigate to "Branding"
   - Upload logo, set colors, configure watermark
2. **Monitor Usage**:
   - Navigate to "Billing & Usage"
   - View credit allocation, usage stats, storage
3. **Generate API Keys**:
   - In "Billing" section, scroll to "API Keys"
   - Enter label and click "Generate New API Key"
   - Copy and securely store the generated key

### For Platform Admins

1. **Manage Tenants**:
   - Navigate to "Admin" → "Manage Tenants"
   - Enable/disable tenants
2. **Manage Global Styles**:
   - Navigate to "Admin" → "Manage Global Styles"
   - Create new global avatar styles available to all tenants

---

## API Usage

### Authentication

Include your API key in the `X-API-KEY` header:

```bash
X-API-KEY: sk_your_api_key_here
```

### Create Avatar Job

```bash
POST /api/v1/avatar-jobs/create
Content-Type: application/json
X-API-KEY: sk_your_api_key

{
  "user_reference": "user-123",
  "style_ids": [1, 2, 3],
  "face_image_urls": ["https://example.com/face1.jpg", "https://example.com/face2.jpg"]
}
```

**Response**:
```json
{
  "status": "success",
  "message": "Job created and processing started",
  "data": {
    "job_id": 42,
    "status": "processing",
    "avatars_requested": 6
  }
}
```

### Get Job Status

```bash
GET /api/v1/avatar-jobs/42
X-API-KEY: sk_your_api_key
```

**Response**:
```json
{
  "status": "success",
  "data": {
    "job_id": 42,
    "title": "API Job - 2025-01-15 10:30:00",
    "status": "completed",
    "avatars_requested": 6,
    "avatars_generated": 6,
    "credits_cost": 60,
    "created_at": "2025-01-15 10:30:00"
  }
}
```

### List Generated Avatars

```bash
GET /api/v1/avatar-jobs/42/avatars
X-API-KEY: sk_your_api_key
```

**Response**:
```json
{
  "status": "success",
  "data": {
    "job_id": 42,
    "total_avatars": 6,
    "avatars": [
      {
        "id": 123,
        "style_name": "Anime Neon",
        "image_url": "http://localhost/storage/uploads/avatars/1/avatar_123.jpg",
        "thumbnail_url": "http://localhost/storage/uploads/avatars/1/avatar_123_thumb.jpg",
        "width": 512,
        "height": 512,
        "download_count": 0,
        "created_at": "2025-01-15 10:30:15"
      }
    ]
  }
}
```

---

## Testing

Run the included test suite:

```bash
# Test database connection
php tests/test_db_connection.php

# Test authentication
php tests/test_auth_login.php

# Test face upload validation
php tests/test_face_upload_validation.php

# Test avatar job creation
php tests/test_create_avatar_job.php

# Test avatar pipeline
php tests/test_run_avatar_pipeline.php

# Test credits enforcement
php tests/test_credits_enforcement.php

# Test API key authentication
php tests/test_api_key_auth.php

# Test tenant isolation
php tests/test_tenant_isolation.php
```

---

## Project Structure

```
/app
  /core              # MVC framework core classes
  /controllers       # Application controllers
  /models            # Database models
  /views             # HTML templates
  /helpers           # Helper utilities
  /middleware        # Request middleware
/config              # Configuration files
/public              # Web root (index.php, assets)
/storage
  /uploads           # User uploaded files
  /logs              # Application logs
/tests               # Test scripts
database.sql         # Database schema & seed data
README.md            # This file
```

---

## Roadmap & Scalability Suggestions

### Current Implementation
- Simulated avatar generation (placeholder pipeline)
- Local file storage
- Synchronous job processing
- Simple in-memory rate limiting

### Production Enhancements

1. **Real AI Integration**:
   - Integrate with Stable Diffusion API or similar
   - Implement Python microservice for AI model inference
   - Use job queues (Redis, RabbitMQ) for async processing

2. **Cloud Storage**:
   - Replace local storage with S3, Google Cloud Storage, or Azure Blob
   - Implement CDN for avatar delivery
   - Per-tenant storage buckets for better isolation

3. **Background Processing**:
   - Implement Laravel Horizon or Symfony Messenger for job queues
   - Use workers to process avatar generation jobs
   - Add job progress tracking with WebSockets

4. **Enhanced Security**:
   - Implement rate limiting with Redis
   - Add IP-based access controls
   - Implement signed URLs for avatar downloads
   - Add two-factor authentication

5. **Monitoring & Analytics**:
   - Integrate with Sentry for error tracking
   - Add Google Analytics or Plausible
   - Implement Prometheus metrics
   - Add per-tenant usage analytics dashboards

6. **Performance**:
   - Add Redis caching layer
   - Implement database read replicas
   - Add full-text search with Elasticsearch
   - Optimize images with WebP format

---

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

This project is open-source and available under the MIT License.

---

## Support

For issues, questions, or feature requests, please open an issue on GitHub.

---

## Credits

**SplashAvatar** - Built with PHP, MySQL, and passion for clean, secure, scalable code.

---

**Happy Avatar Generating!** 🎨
