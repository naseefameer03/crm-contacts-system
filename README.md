# CRM Contacts Filter & Export - Laravel Application

## ✨ Overview

This is a mini CRM-style Laravel application designed to manage and filter 1 million contact records, with features including:

* Contact filtering by status, company, and creation date.
* Search functionality (name/email).
* Paginated results display.
* Export filtered results to CSV.

---

## 📁 Project Setup

### 1. Clone the Repository

```bash
git clone https://github.com/naseefameer03/crm-contacts-system.git
cd crm-contacts
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run dev
```

### 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env` file with correct **database credentials**.

### 4. Run Migrations and Seeders

```bash
php artisan migrate
php artisan db:seed --class=ContactSeeder
```

> The seeder will generate 1 million realistic contact records using Faker.

### 5. Serve the Application

```bash
php artisan serve
```

Visit: [http://localhost:8000](http://localhost:8000)

---

## 🔍 Features

* Filter contacts by **status**, **company**, and **created date**.
* Global **search** (by name/email).
* **Paginated table** to navigate large datasets.
* Export **filtered contacts** to a **CSV file** using Laravel Excel.

---

## ⚡ Performance Considerations

* Database columns like `status`, `company`, and `created_at` are **indexed**.
* **Chunked inserts** for seeding 1 million records efficiently.
* **FromQuery export** used to handle large exports with optimized memory.
* Uses **pagination** to avoid memory overload on the UI.

---

## 📹 Video Walkthrough

Watch the demo video here:
[🔗 Click to Watch](https://your-video-link.com)

---

## 📄 Tech Stack

* Laravel 12
* MySQL / MariaDB
* Laravel Breeze (auth scaffolding)
* Laravel Excel (CSV export)
* Tailwind CSS + Blade templates

---

## ✉ Contact

For any questions or feedback, feel free to contact:
**Naseef Ameer**
Senior PHP/Laravel Developer
[naasfameer@gmail.com](mailto:naasfameer@gmail.com)