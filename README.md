# QuizApp_final

A web‑based quiz management system built with PHP. It provides separate interfaces for **Admins**, **Instructors**, and **Students**, allowing easy creation of lessons, exercises, and tracking of quiz results.

---

## Overview

QuizApp_final lets educational institutions manage quizzes online:

- Admins can add/edit instructors, lessons, and exercises, and view overall results.
- Instructors can create exercises and lessons for their courses.
- Students (via the public front‑end) can take quizzes and view feedback.

All data is stored in a MySQL database (`Database/quiz_db.sql`). The project follows a simple MVC‑like structure with dedicated `admin/` and `instructor/` directories.

---

## Features

| Feature | Description |
|---------|-------------|
| **Admin Dashboard** | Manage instructors, lessons, exercises, and view aggregated results. |
| **Instructor Dashboard** | Add lessons and exercises, view own results. |
| **Quiz Engine** | Serve multiple‑choice questions, record answers, and calculate scores. |
| **Result Reporting** | Exportable result tables for each exercise. |
| **Responsive UI** | Clean layout powered by `css/style.css`. |
| **Support & Feedback** | Contact form (`contact_support.php`) and feedback page (`feedback.php`). |
| **Secure Authentication** | Separate login pages for admins (`admin_login.php`) and instructors (`instructor/config.php`). |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL (schema in `Database/quiz_db.sql`) |
| **Front‑end** | HTML5, CSS3 (custom stylesheet `css/style.css`) |
| **Server** | Apache / Nginx (any LAMP stack) |
| **Version Control** | Git (GitHub) |

---

## Installation

### Prerequisites

- PHP 7.4 or newer with `mysqli` extension enabled
- MySQL server
- A web server (Apache/Nginx) configured to serve PHP files
- Composer (optional, only if you add third‑party packages)

### Steps

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/QuizApp_final.git
   cd QuizApp_final
   ```

2. **Create the database**

   ```bash
   mysql -u root -p < Database/quiz_db.sql
   ```

   > Adjust the username/password as needed.

3. **Configure database connection**

   Edit `config.php` (and `admin/config.php`, `instructor/config.php` if they exist) and replace the placeholder values with your own credentials:

   ```php
   define('DB_HOST', 'YOUR_DB_HOST');
   define('DB_USER', 'YOUR_DB_USER');
   define('DB_PASS', 'YOUR_DB_PASSWORD');
   define('DB_NAME', 'YOUR_DB_NAME');
   ```

4. **Set file permissions**

   Ensure the `admin/uploads/` directory is writable by the web server:

   ```bash
   chmod -R 755 admin/uploads
   ```

5. **Start the server**

   - **Apache**: Place the project folder inside `htdocs` (or the configured document root) and restart Apache.
   - **Built‑in PHP server** (for quick testing):

     ```bash
     php -S localhost:8000
     ```

6. **Access the application**

   - Admin login: `http://localhost/QuizApp_final/admin/admin_login.php`
   - Instructor login: `http://localhost/QuizApp_final/instructor/config.php`
   - Public home page: `http://localhost/QuizApp_final/index.php`

---

## Usage

### Admin Workflow

1. Log in via `admin/admin_login.php`.
2. Use the navigation bar (`admin/admin_navbar.php`) to:
   - **Add Instructors** – `admin/add_instructor.php`
   - **Add Lessons