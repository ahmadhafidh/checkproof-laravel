<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Project Setup and API Testing Instructions

This guide will help you set up the project, run migrations, seed the database, and test the APIs.

## 1. Setup Environment

Copy the example environment file to create your own `.env` file:

```bash
cp .env.example .env
```

Update the `.env` file with your database and other configuration settings.

## 2. Run Migrations

Run Laravel migrations to create the database tables:

```bash
php artisan migrate
```

## 3. Live API Testing

You can test the live APIs here:

[checkproof.idkoding.com](https://checkproof.idkoding.com)

## 4. Postman Documentation

Access the live Postman documentation for all API endpoints:

[Postman Docs Live](https://documenter.getpostman.com/view/26950655/2sB3WjxP2D)

## 5. Seed Database

sample data for testing `orders_count` on Orders Table:

```bash
php artisan db:seed
```