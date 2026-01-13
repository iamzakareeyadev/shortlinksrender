# JND ShortLinks - System Architecture & Design

## ภาพรวมระบบ (System Overview)
JND ShortLinks เป็นระบบย่อ URL ที่พัฒนาด้วย **Laravel Framework** ใช้สถาปัตยกรรมแบบ **MVC (Model-View-Controller)** ร่วมกับ **MySQL** เป็นฐานข้อมูล และ **Bootstrap 5** สำหรับ Frontend

## สถาปัตยกรรมระบบ (Architecture)
```
┌─────────────────────────────────────────────────────────────────┐
│                         Client (Browser)                         │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Laravel Application                         │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │   Routes    │→ │ Middleware  │→ │      Controllers        │  │
│  │  (web.php)  │  │(Auth/Admin) │  │(Url/Admin/Auth)         │  │
│  └─────────────┘  └─────────────┘  └───────────┬─────────────┘  │
│                                                 │                │
│  ┌─────────────────────────────────────────────▼─────────────┐  │
│  │                    Models (Eloquent ORM)                   │  │
│  │              User  │  Url  │  UrlClick                     │  │
│  └─────────────────────────────────────────────┬─────────────┘  │
│                                                 │                │
│  ┌─────────────────────────────────────────────▼─────────────┐  │
│  │                    Cache Layer (Redis/File)                │  │
│  └───────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                     MySQL Database                               │
│         users  │  urls  │  url_clicks  │  sessions              │
└─────────────────────────────────────────────────────────────────┘
```

## การออกแบบเพื่อประสิทธิภาพสูง (Performance Design)

| เทคนิค | รายละเอียด |
|--------|------------|
| **Caching** | ใช้ Cache::remember() สำหรับ URL redirect เพื่อลด Database queries |
| **Database Indexing** | เพิ่ม Index บน `short_code`, `user_id`, `is_active` สำหรับการค้นหาที่รวดเร็ว |
| **Atomic Operations** | ใช้ `increment()` แทน read-update-write สำหรับนับ clicks |
| **Separated Analytics** | แยกตาราง `url_clicks` เพื่อไม่กระทบ performance ของการ redirect |

## โมดูลหลัก (Core Modules)

1. **User Module** - สมัครสมาชิก/เข้าสู่ระบบด้วย Email + Password
2. **URL Shortener Module** - สร้าง Short URL, Custom code, วันหมดอายุ, Analytics
3. **Admin Module** - จัดการ URLs/Users, Dashboard สถิติ, เปิด/ปิด URLs

## ความปลอดภัย (Security)
- Password Hashing (bcrypt), CSRF Protection, XSS Prevention
- Middleware แยก Admin/User, Authorization checks ในทุก Controller
