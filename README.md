<h1 style="border-bottom: none;">OneTAP - Backend</h1>

> **OneTAP (One Teaching and Academic Platform)** is a lightweight yet scalable Learning Management System (LMS). This repository contains the backend API built with Laravel. 

---

<h2 style="border-bottom: none;">📖 Project Overview</h2>

OneTAP provides essential LMS features such as:  
- Course and class management  
- Student and instructor accounts  
- Role-based permissions (admin, superadmin, etc.)  
- Attendance and assignment tracking *(future scope)*  
- Deployment-ready RESTful API for frontend integration  

This is the backend API service for OneTAP, built with Laravel and MySQL.  

---

<h2 style="border-bottom: none;">🛠️ Tech Stack</h2>

- **Framework**: Laravel (PHP)  
- **Database**: MySQL  
- **Authentication**: Laravel Sanctum *(planned)*  
- **Architecture**: RESTful API, MVC pattern  

---

<h2 style="border-bottom: none;">⚙️ Setup & Installation</h2>

### Prerequisites  
- PHP ≥ 8.1  
- Composer  
- MySQL (XAMPP, Docker, or standalone)  
- Git  

---

### Installation  
```bash
# Clone the repo
git clone https://github.com/your-username/onetap-backend.git

# Go into the project
cd onetap-backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start local server
php artisan serve
```

---

<h2 style="border-bottom: none;">🗄️ Database Configuration</h2>

Update your `.env` file with your database credentials:  
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=onetap_db
DB_USERNAME=root
DB_PASSWORD=
```

---

<h2 style="border-bottom: none;">🚀 Deployment</h2>

TBA – planned for Render (backend) and Vercel (frontend).  

---

<h2 style="border-bottom: none;">👥 Team Information</h2>

-  Jam - Project Manager
-  Mythrynne – Developer
-  Shandong - User Interface/Experience
-  Ate Jo - Software Quality Assurance

---

<h2 style="border-bottom: none;">📌 Status</h2>

🚧 Currently under development.  
MVP excludes **Grade Input/Monitor** but includes **core LMS features** such as class management, user roles, and authentication.  
