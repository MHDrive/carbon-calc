<p align="center">
    <img src="https://github.com/filamentphp/filament/assets/41773797/8d5a0b12-4643-4b5c-964a-56f0db91b90a" alt="Banner" style="width: 100%; max-width: 800px;" />
</p>

Filament is a collection of full-stack components for accelerated Laravel development. They are beautifully designed, intuitive to use, and fully extensible - the perfect starting point for your next Laravel app. Why waste time building the same features over and over again?

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# CarbonCalc

adalah sebuah proyek yang bertujuan untuk menyediakan fitur kalkulator untuk menghitung cadangan carbon. Aplikasi ini dikembangkan dengan menggunakan framework Laravel 11 dan Filament 3.1


## Dokumentasi
> [Instalasi & Cara Penggunaan](#cara-penggunaan)
> ### Instalasi 
> - Clone/Download Source code proyek ini.
> 
> - Install dependencies yang diperlukan dengan cara menjalankan perintah berikut diterminal:
> 
>   ```shell
>   composer install
>   ```
>  
>  - cari file dengan nama `.env.example`, copy paste dan rename menjadi `.env` 
>  - Buka file `.env` lalu sesuaikan `DB_DATABASE, DB_USERNAME, DB_PASSWORD` dengan MySQL anda, namun apabila anda menggunakan mysql default, anda bisa mengabaikan hal tersebut.
>
> - selanjutnya jalankan perintah berikut diterimal step by step:
>   ```shell
>    php artisan key:generate
>
>    php artisan storage:link
>
>    npm install
>
>    npm run build
>
>    npm run dev
>   ```
> - lalu buka terminal baru, dan jalankan perintah berikut step by step:
>   ```shell
>     php artisan migrate --seed
> 
>     php artisan serve
>   ```
>  - jalankan ini di browser anda http://127.0.0.1:8000/admin
>
>  - login sebagai admin:
>    ```shell 
>     email: admin@example.com
>     password: password
>    ```
>
>  - login sebagai user:
>    ```shell 
>    email: guest@gmail.com
>    password: password
>    ```
