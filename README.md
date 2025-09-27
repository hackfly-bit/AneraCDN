# Laravel CDN

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/username/laravel-cdn/tests.yml?style=for-the-badge)](https://github.com/username/laravel-cdn/actions)

> 🚀 **Aplikasi Content Delivery Network (CDN) modern yang dibangun dengan Laravel 12**

Sebuah platform manajemen file yang powerful, dirancang untuk mengelola, mengoptimalkan, dan mendistribusikan file secara efisien dengan fitur keamanan tingkat enterprise dan API yang lengkap.

## 📑 Table of Contents

- [✨ Features](#-features)
- [🛠️ Tech Stack](#️-tech-stack)
- [📦 Installation](#-installation)
- [🚀 Quick Start](#-quick-start)
- [📖 Usage](#-usage)
- [⚙️ Configuration](#️-configuration)
- [🧪 Testing](#-testing)
- [📚 API Documentation](#-api-documentation)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)
- [🆘 Support](#-support)

## 🎯 About

Laravel CDN adalah platform manajemen file yang menyediakan solusi lengkap untuk upload, penyimpanan, optimisasi, dan distribusi file. Aplikasi ini mendukung berbagai jenis file termasuk gambar, video, dan dokumen dengan fitur optimisasi otomatis, thumbnail generation, dan konversi WebP.

### 🌟 Key Highlights

- 🔒 **Enterprise Security** - Role-based access control dengan API key management
- ⚡ **High Performance** - Optimisasi gambar otomatis dan CDN integration
- 🎨 **Modern UI** - Dashboard responsif dengan dark mode support
- 🔌 **RESTful API** - Complete API untuk integrasi dengan aplikasi lain
- 📊 **Analytics** - Real-time monitoring dan usage statistics

## 🚀 Demo

> **Live Demo:** [https://laravel-cdn-demo.com](https://laravel-cdn-demo.com)
> 
> **Test Credentials:**
> - Email: `demo@example.com`
> - Password: `password`

## ✨ Features

### 🔐 Autentikasi & Otorisasi
- Sistem autentikasi berbasis Laravel Breeze
- Manajemen role dan permission menggunakan Spatie Permission
- API Key management untuk akses programmatic
- Laravel Sanctum untuk API authentication

### 📁 Manajemen File
- Upload file dengan drag & drop interface
- Dukungan multiple file types (gambar, video, dokumen)
- Optimisasi gambar otomatis dengan Intervention Image
- Thumbnail generation untuk preview
- Konversi WebP untuk optimisasi bandwidth
- Metadata extraction dan storage
- File versioning dan backup

### 🚀 CDN & Performance
- URL CDN yang dapat dikonfigurasi
- Caching strategy yang optimal
- Lazy loading untuk gambar
- Compression otomatis
- Rate limiting untuk upload

### 📊 Analytics & Monitoring
- Tracking download count
- File access analytics
- User activity monitoring
- Storage usage statistics
- Performance metrics

### 🔒 Keamanan
- File access control (public/private)
- Secure file serving
- API rate limiting
- File type validation
- Malware scanning capability

### 🎨 User Interface
- Dashboard yang responsif dengan Tailwind CSS
- Dark mode support
- Alpine.js untuk interaktivitas
- Mobile-friendly design
- Real-time notifications

## 🛠️ Tech Stack

<div align="center">

### Backend
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql)

### Frontend
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=flat-square&logo=tailwind-css)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3-8BC34A?style=flat-square&logo=alpine.js)
![Vite](https://img.shields.io/badge/Vite-5-646CFF?style=flat-square&logo=vite)

### Tools & Services
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat-square&logo=docker)
![Redis](https://img.shields.io/badge/Redis-DC382D?style=flat-square&logo=redis)
![AWS S3](https://img.shields.io/badge/AWS_S3-569A31?style=flat-square&logo=amazon-s3)

</div>

| Category | Technologies |
|----------|-------------|
| **Backend** | PHP 8.4, Laravel 12, Laravel Sanctum, Spatie Permission |
| **Database** | MySQL, Redis (caching) |
| **Frontend** | Tailwind CSS 3, Alpine.js 3, Vite, Axios |
| **Image Processing** | Intervention Image, WebP conversion |
| **Development** | Laravel Pint, PHPUnit, Laravel Sail, Laravel Pail |
| **Storage** | Local, AWS S3, CDN integration |

## 🚀 Quick Start

### Using Docker (Recommended)

```bash
# Clone the repository
git clone https://github.com/username/laravel-cdn.git
cd laravel-cdn

# Start with Laravel Sail
./vendor/bin/sail up -d

# Install dependencies and setup
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run build
```

### Manual Installation

<details>
<summary>Click to expand manual installation steps</summary>

## 📦 Installation

### Prerequisites

| Requirement | Version | Status |
|-------------|---------|--------|
| PHP | >= 8.2 | ✅ Required |
| Composer | Latest | ✅ Required |
| Node.js | >= 18 | ✅ Required |
| MySQL/PostgreSQL | 8.0+ / 13+ | ✅ Required |
| Redis | Latest | ⚠️ Optional |

### Step-by-Step Installation

#### 1. Clone Repository
```bash
git clone https://github.com/username/laravel-cdn.git laravel-cdn
cd laravel-cdn
```

#### 2. Install Dependencies
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

#### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4. Database Configuration
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_cdn
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### 5. Storage Configuration
```env
FILESYSTEM_DISK=local
CDN_URL=https://your-cdn-domain.com
```

#### 6. Database Migration
```bash
# Run migrations and seeders
php artisan migrate --seed

# Create storage link
php artisan storage:link
```

#### 7. Build Assets
```bash
# Production build
npm run build

# Or development with watch
npm run dev
```

#### 8. Start Application
```bash
# Development server
php artisan serve

# Or using Laravel Sail
./vendor/bin/sail up
```

</details>

## 📖 Usage

### 🌐 Web Dashboard

<table>
<tr>
<td width="50%">

#### 🔐 Authentication
```
📍 /register - Create new account
📍 /login    - User login
📍 /dashboard - Main dashboard
```

#### 📁 File Management
- **Upload**: Drag & drop interface
- **Organize**: Folders and categories
- **Optimize**: Automatic image processing
- **Share**: Public/private file sharing

</td>
<td width="50%">

#### 🔑 API Key Management
```
📍 /dashboard/api - Manage API keys
📍 /dashboard/stats - Usage analytics
📍 /dashboard/settings - Configuration
```

#### 📊 Analytics
- Real-time file statistics
- Download tracking
- Storage usage monitoring
- Performance metrics

</td>
</tr>
</table>

### 🔌 API Usage

#### Authentication
```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password","password_confirmation":"password"}'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}'
```

#### File Operations
```bash
# Upload file
curl -X POST http://localhost:8000/api/files/upload \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@/path/to/your/file.jpg" \
  -F "is_public=true"

# Get file list
curl -X GET http://localhost:8000/api/files \
  -H "Authorization: Bearer YOUR_TOKEN"

# Download file
curl -X GET http://localhost:8000/api/files/{slug}/download \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### File Access

#### Public Files
```html
<!-- Direct access -->
<img src="https://your-domain.com/storage/path/to/file.jpg" alt="Image">

<!-- CDN URL -->
<img src="https://your-cdn-domain.com/storage/path/to/file.jpg" alt="Image">
```

#### Private Files
```html
<!-- Secure download link -->
<a href="https://your-domain.com/file/{slug}/download">Download File</a>
```

## ⚙️ Konfigurasi

### Environment Variables

```env
# CDN Configuration
CDN_URL=https://your-cdn-domain.com
FILESYSTEM_DISK=s3  # local, s3, etc.

# Image Optimization
IMAGE_OPTIMIZATION_ENABLED=true
WEBP_CONVERSION_ENABLED=true
THUMBNAIL_GENERATION_ENABLED=true

# Rate Limiting
UPLOAD_RATE_LIMIT=10  # uploads per minute
API_RATE_LIMIT=60     # requests per minute

# File Limits
MAX_FILE_SIZE=10240   # KB
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf,doc,docx,mp4,avi
```

### Storage Configuration

#### Local Storage
```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
]
```

#### S3 Storage
```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
]
```

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Test dengan coverage
php artisan test --coverage

# Test spesifik
php artisan test --filter=FileUploadTest
```

## 📈 Performance Optimization

### Caching
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### Queue Setup
```bash
# Setup Redis untuk queue
php artisan queue:table
php artisan migrate

# Jalankan queue worker
php artisan queue:work
```

### Image Optimization
- Automatic WebP conversion untuk browser yang mendukung
- Thumbnail generation untuk preview cepat
- Progressive JPEG untuk loading yang lebih baik
- Lazy loading implementation

## 🔧 Development

### Code Style
```bash
# Format code dengan Laravel Pint
vendor/bin/pint

# Check code style
vendor/bin/pint --test
```

### Database
```bash
# Create migration
php artisan make:migration create_files_table

# Create model dengan factory dan seeder
php artisan make:model File -mfs

# Refresh database
php artisan migrate:refresh --seed
```

### Debugging
```bash
# View logs
php artisan pail

# Tinker
php artisan tinker
```

## 📚 API Documentation

### Endpoints

#### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `GET /api/auth/me` - Get current user
- `POST /api/auth/logout` - Logout
- `POST /api/auth/logout-all` - Logout from all devices

#### Files
- `GET /api/files` - List files
- `POST /api/files/upload` - Upload file
- `GET /api/files/{id}` - Get file details
- `PUT /api/files/{id}` - Update file
- `DELETE /api/files/{id}` - Delete file
- `GET /api/files/{slug}/download` - Download file
- `GET /api/files/stats` - Get file statistics

#### Health Check
- `GET /api/health` - Application health status

## 📸 Screenshots

<div align="center">

### Dashboard Overview
![Dashboard](https://via.placeholder.com/800x400/1f2937/ffffff?text=Dashboard+Screenshot)

### File Management
![File Management](https://via.placeholder.com/800x400/1f2937/ffffff?text=File+Management+Screenshot)

### API Documentation
![API Docs](https://via.placeholder.com/800x400/1f2937/ffffff?text=API+Documentation+Screenshot)

</div>

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

### Quick Start for Contributors

1. **Fork & Clone**
   ```bash
   git clone https://github.com/your-username/laravel-cdn.git
   cd laravel-cdn
   ```

2. **Create Feature Branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```

3. **Make Changes & Test**
   ```bash
   # Make your changes
   vendor/bin/pint  # Format code
   php artisan test # Run tests
   ```

4. **Commit & Push**
   ```bash
   git commit -m "feat: add amazing feature"
   git push origin feature/amazing-feature
   ```

5. **Create Pull Request**
   - Open PR with clear description
   - Link related issues
   - Ensure CI passes

### Development Guidelines

| Guideline | Description |
|-----------|-------------|
| **Code Style** | Follow PSR-12, use Laravel Pint |
| **Testing** | Write tests for new features |
| **Documentation** | Update docs for API changes |
| **Commits** | Use [Conventional Commits](https://conventionalcommits.org/) |
| **Security** | Report vulnerabilities privately |

### Development Setup

```bash
# Install development dependencies
composer install --dev
npm install

# Setup pre-commit hooks
composer run post-install-cmd

# Run development server
composer run dev
```

## 📄 License

This project is licensed under the [MIT License](LICENSE) - see the LICENSE file for details.

## 🗺️ Roadmap

- [ ] **v2.0** - Multi-tenant support
- [ ] **v2.1** - Advanced image filters
- [ ] **v2.2** - Video transcoding
- [ ] **v2.3** - AI-powered content tagging
- [ ] **v2.4** - GraphQL API
- [ ] **v2.5** - Mobile app

See the [open issues](https://github.com/username/laravel-cdn/issues) for a full list of proposed features and known issues.

## 🆘 Support

<div align="center">

### 💬 Get Help

[![GitHub Issues](https://img.shields.io/github/issues/username/laravel-cdn?style=for-the-badge)](https://github.com/username/laravel-cdn/issues)
[![GitHub Discussions](https://img.shields.io/github/discussions/username/laravel-cdn?style=for-the-badge)](https://github.com/username/laravel-cdn/discussions)
[![Discord](https://img.shields.io/discord/123456789?style=for-the-badge&logo=discord)](https://discord.gg/laravel-cdn)

</div>

| Type | Link | Description |
|------|------|-------------|
| 🐛 **Bug Reports** | [Create Issue](../../issues/new?template=bug_report.md) | Report bugs and errors |
| 💡 **Feature Requests** | [Create Issue](../../issues/new?template=feature_request.md) | Suggest new features |
| ❓ **Questions** | [Discussions](../../discussions) | General questions and help |
| 💬 **Chat** | [Discord](https://discord.gg/laravel-cdn) | Real-time community chat |

## 🙏 Acknowledgments

Special thanks to these amazing projects and contributors:

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC34A?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)

</div>

- **[Laravel](https://laravel.com)** - The PHP framework for web artisans
- **[Tailwind CSS](https://tailwindcss.com)** - Utility-first CSS framework
- **[Alpine.js](https://alpinejs.dev)** - Lightweight JavaScript framework
- **[Intervention Image](http://image.intervention.io)** - Image processing library
- **[Spatie](https://spatie.be)** - Amazing Laravel packages
- **All contributors** who have helped make this project better

## ⭐ Star History

[![Star History Chart](https://api.star-history.com/svg?repos=username/laravel-cdn&type=Date)](https://star-history.com/#username/laravel-cdn&Date)

---

<div align="center">

**Laravel CDN** - Modern file management and CDN solution 🚀

[![Made with ❤️ by Laravel Community](https://img.shields.io/badge/Made%20with%20❤️%20by-Laravel%20Community-FF2D20?style=for-the-badge)](https://github.com/username/laravel-cdn)

**[⬆ Back to Top](#laravel-cdn)**

</div>