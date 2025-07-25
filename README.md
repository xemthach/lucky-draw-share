# 🎯 Lucky Draw System

A complete lucky draw system with frontend and backend API for sharing results.

## Features

### Frontend
- ✅ **Single HTML file** - No external dependencies
- ✅ **Bilingual support** - English and Vietnamese
- ✅ **Crypto-based randomness** - Uses `window.crypto.getRandomValues()`
- ✅ **Fisher-Yates shuffle** - Fair and unbiased selection
- ✅ **Responsive design** - Works on mobile/tablet/desktop
- ✅ **Theme customization** - Light, Dark, and Festival modes
- ✅ **Winner history** - Tracks all winners with timestamps
- ✅ **Export functionality** - Download winner list as .txt
- ✅ **Share functionality** - Share results via API

### Backend API
- ✅ **RESTful API** - Clean PHP 8.1+ implementation
- ✅ **Database storage** - MySQL with JSON support
- ✅ **Unique share IDs** - 8-character alphanumeric codes
- ✅ **Auto-expiry** - Links expire after 30 days
- ✅ **Cleanup script** - Cron-compatible cleanup
- ✅ **Security** - Input validation and SQL injection protection

## 🚀 Quick Start

### 1. Database Setup

```sql
-- Create database and table
mysql -u root -p < database/schema.sql
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Edit database settings
nano .env
```

### 3. Install Dependencies

```bash
# Install Composer dependencies
composer install
```

### 4. Web Server Configuration

#### Apache (.htaccess included)
The `.htaccess` file handles URL rewriting for:
- `/share/{id}` → `public/share.php`
- `/api/save-draw` → `public/api/save-draw.php`

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.html;
}

location /share/ {
    rewrite ^/share/([a-z0-9]{8})$ /public/share.php?share_id=$1 last;
}

location /api/ {
    rewrite ^/api/save-draw$ /public/api/save-draw.php last;
}
```

## 📁 Project Structure

```
lucky-draw/
├── config/
│   └── database.php          # Database configuration
├── src/
│   ├── Controller/
│   │   ├── ApiController.php # API endpoints
│   │   └── ShareController.php # Share page controller
│   ├── Database/
│   │   └── Connection.php    # PDO connection wrapper
│   ├── Model/
│   │   └── SharedDraw.php    # Data model
│   ├── Repository/
│   │   └── SharedDrawRepository.php # Database operations
│   └── Service/
│       └── SharedDrawService.php # Business logic
├── public/
│   ├── api/
│   │   └── save-draw.php     # API endpoint
│   └── share.php             # Share page handler
├── scripts/
│   └── cleanup.php           # Cron cleanup script
├── database/
│   └── schema.sql            # Database schema
├── index.html                # Main frontend app
├── composer.json             # PHP dependencies
├── .htaccess                 # URL rewriting
├── .env.example              # Environment template
└── README.md                 # This file
```

## 🔌 API Documentation

### POST /api/save-draw

Save winners and get a shareable URL.

**Request:**
```json
{
    "winners": ["John Doe", "Jane Smith", "Mike Johnson"]
}
```

**Response:**
```json
{
    "success": true,
    "share_id": "abc12345",
    "share_url": "https://example.com/share/abc12345",
    "created_at": "2024-01-15 14:30:00",
    "winners_count": 3
}
```

**Error Response:**
```json
{
    "error": "Winners array is required"
}
```

### GET /share/{share_id}

Display shared draw results.

**Parameters:**
- `share_id` (8 characters, alphanumeric)

**Response:**
- HTML page with winner list
- 404 if not found
- Expired message if older than 30 days

## 🗄️ Database Schema

```sql
CREATE TABLE shared_draws (
    id INT AUTO_INCREMENT PRIMARY KEY,
    share_id VARCHAR(8) NOT NULL UNIQUE,
    winners JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_share_id (share_id),
    INDEX idx_created_at (created_at)
);
```

## 🔧 Maintenance

### Cleanup Expired Records

Set up a cron job to clean expired records:

```bash
# Add to crontab (runs daily at 2 AM)
0 2 * * * /usr/bin/php /path/to/lucky-draw/scripts/cleanup.php

# Or run manually
php scripts/cleanup.php
```

### Manual Cleanup

```bash
# Count expired records
mysql -u root -p random -e "SELECT COUNT(*) FROM shared_draws WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);"

# Delete expired records
mysql -u root -p random -e "DELETE FROM shared_draws WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);"
```

## 🌐 Frontend Integration

The frontend automatically calls the API when you click "Share Winners":

1. **Sends winners** to `/api/save-draw`
2. **Receives share URL** from API
3. **Shows modal** with copy/open options
4. **Displays results** at `/share/{id}`

## 🔒 Security Features

- **Input validation** - All API inputs are validated
- **SQL injection protection** - Uses PDO prepared statements
- **XSS protection** - Output is properly escaped
- **CORS headers** - API supports cross-origin requests
- **Rate limiting** - Consider adding rate limiting for production

## 🎨 Customization

### Themes
The frontend supports three themes:
- **Light** - Default clean theme
- **Dark** - Dark mode for low-light environments
- **Festival** - Colorful theme for celebrations

### Languages
- **English** - Default language
- **Vietnamese** - Full translation support

### Styling
All styles are in the HTML file using CSS custom properties for easy theming.

## 🚀 Deployment

### Production Checklist

- [ ] Set up MySQL database
- [ ] Configure environment variables
- [ ] Install Composer dependencies
- [ ] Set up web server (Apache/Nginx)
- [ ] Configure URL rewriting
- [ ] Set up SSL certificate
- [ ] Configure cron job for cleanup
- [ ] Test API endpoints
- [ ] Test sharing functionality

### Environment Variables

```env
# Database
DB_HOST=localhost
DB_PORT=3306
DB_NAME=random
DB_USER=your_username
DB_PASS=your_password

# Application
APP_ENV=production
APP_DEBUG=false
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the MIT License.

## 🆘 Support

For issues and questions:
1. Check the documentation
2. Review the code comments
3. Test with different browsers
4. Check server error logs
5. Create an issue with details 
