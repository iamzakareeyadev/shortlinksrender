# JND ShortLinks - URL Shortener

ระบบย่อ URL แบบเรียบง่าย พัฒนาด้วย Laravel Framework คล้ายกับ Bitly

🌐 **Live Demo**: [https://jnd-shortlinks.onrender.com](https://jnd-shortlinks.onrender.com)

---

## ✨ Features

### 1. User Account Module
- สมัครสมาชิก / เข้าสู่ระบบด้วย Email + Password
- ระบบ Remember me
- Password hashing (bcrypt)

### 2. URL Shortener Module
- สร้าง Short URL อัตโนมัติ (6 ตัวอักษร)
- กำหนด Custom short code ได้ (4-10 ตัวอักษร)
- ตั้งวันหมดอายุ URL
- ติดตามสถิติการคลิก (จำนวน, อุปกรณ์, เบราว์เซอร์)
- เปิด/ปิด URL ได้

### 3. Admin Module
- Dashboard แสดงสถิติภาพรวม
- จัดการ URLs ทั้งหมด (ค้นหา, กรอง, เปิด/ปิด, ลบ)
- จัดการผู้ใช้ (ให้/ลดสิทธิ์ Admin, ลบ)

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.2, Laravel 11 |
| Frontend | Blade Template, Bootstrap 5 |
| Database | MySQL (Local) / PostgreSQL (Production) |
| Caching | File Cache (รองรับ Redis) |
| Deployment | Docker, Render.com |

---

## 📦 Installation (Local Development)

### 1. Clone โปรเจค
```bash
git clone https://github.com/YOUR_USERNAME/jnd-shortlinks.git
cd jnd-shortlinks
```

### 2. ติดตั้ง Dependencies
```bash
composer install
```

### 3. ตั้งค่า Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. แก้ไข `.env` สำหรับ Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jnd_shortlinks
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. สร้าง Database และ Migrate
```bash
# สร้าง database ใน MySQL ก่อน
mysql -u root -p -e "CREATE DATABASE jnd_shortlinks"

# รัน migrations และ seed
php artisan migrate --seed
```

### 6. รัน Development Server
```bash
php artisan serve
```

เปิด http://localhost:8000

---

## 👤 บัญชีทดสอบ

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| User | test@example.com | password |

---


## 📁 Project Structure

```
jnd-shortlinks/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/AuthController.php    # Login/Register
│   │   │   ├── UrlController.php          # URL CRUD
│   │   │   └── AdminController.php        # Admin functions
│   │   └── Middleware/
│   │       └── AdminMiddleware.php        # Admin guard
│   └── Models/
│       ├── User.php
│       ├── Url.php
│       └── UrlClick.php
├── database/
│   ├── migrations/                        # Database schema
│   └── seeders/DatabaseSeeder.php         # Default users
├── resources/views/
│   ├── layouts/app.blade.php              # Main layout
│   ├── auth/                              # Login/Register pages
│   ├── urls/                              # URL management pages
│   └── admin/                             # Admin pages
├── routes/web.php                         # All routes
├── Dockerfile                             # Docker config
├── render.yaml                            # Render deployment
└── docs/SYSTEM_ARCHITECTURE.md            # Architecture doc
```

---

## ⚡ Performance Optimizations

| Technique | Description |
|-----------|-------------|
| **Caching** | Cache URL lookups สำหรับ redirect (1 ชั่วโมง) |
| **Database Index** | Index บน `short_code`, `user_id`, `is_active` |
| **Atomic Operations** | ใช้ `increment()` สำหรับนับ clicks |
| **Separated Analytics** | แยกตาราง `url_clicks` ไม่กระทบ redirect performance |

---

## 🔒 Security

- ✅ Password Hashing (bcrypt)
- ✅ CSRF Protection
- ✅ XSS Prevention (Blade escaping)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ Authorization middleware (Admin/User)
- ✅ HTTPS forced in production

---

## 📝 API Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/` | Home page |
| GET | `/login` | Login page |
| POST | `/login` | Process login |
| GET | `/register` | Register page |
| POST | `/register` | Process registration |
| POST | `/logout` | Logout |
| GET | `/dashboard` | User dashboard |
| GET | `/urls/create` | Create URL form |
| POST | `/urls` | Store new URL |
| GET | `/urls/{url}` | URL details & stats |
| GET | `/urls/{url}/edit` | Edit URL form |
| PUT | `/urls/{url}` | Update URL |
| DELETE | `/urls/{url}` | Delete URL |
| GET | `/{shortCode}` | Redirect to original URL |
| GET | `/admin` | Admin dashboard |
| GET | `/admin/urls` | Manage all URLs |
| GET | `/admin/users` | Manage users |

---
