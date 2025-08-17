# PHP MVC Project

## 1. مقدمة
هذا المشروع هو هيكلية **MVC** بلغة PHP لإدارة قواعد البيانات، مع دعم **Migrations** و **Rollback**.  
يهدف المشروع لتسهيل إنشاء الجداول، التعديل عليها، وحذفها بطريقة منظمة وآمنة.

المشروع يحتوي على:  
- مجلد `app` لتخزين الملفات الأساسية مثل Controllers و Models.  
- مجلد `core` لتخزين ملفات النظام الأساسية مثل `Database.php`.  
- مجلد `database/migrations` لتخزين ملفات الميجرات.  
- ملف `migrate.php` لتشغيل جميع الميجرات أو عمل rollback.

---

## 2. متطلبات النظام
- PHP >= 8.0
- Composer
- MySQL 
- خادم محلي مثل Laragon، XAMPP أو MAMP

---

## 3. تركيب المشروع

1. استنساخ المشروع:
```bash
git clone <repo-url>
cd PHP-Project

composer install

DB_HOST=127.0.0.1
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
DB_CHARSET=utf8mb4

php database/migrate.php

php database/migrate.php rollback
