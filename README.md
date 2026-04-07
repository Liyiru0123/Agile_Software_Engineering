# Deployment Guide

## 1. Clone the repository

```bash
git clone https://github.com/Liyiru0123/Agile_Software_Engineering.git
cd Agile_Software_Engineering
```

## 2. Install dependencies

```bash
composer install
```

## 3. Initialize the environment

```bash
copy .env.example .env
php artisan key:generate
```

## 4. Configure the database

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_learning
DB_USERNAME=root
DB_PASSWORD=
```

## 5. Import the full database structure and seed data

Use one command only:

```bash
php artisan migrate:fresh --seed
```

This command will:

- create all tables
- import articles and exercises
- import vocabulary
- import admin user data
- import wordle game words

## 6. Start the application

```bash
php artisan config:clear
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```
