<?php

use App\Models\Submission;
use App\Enums\StatusSubmissionEnum;
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withSchedule(function (Schedule $schedule) { // jalankan perintah php artisan schedule:work untuk menjalankan schedule secara local
        $schedule->call(function () {
            $expiredAccess = Submission::where('is_active', true)
                ->where('status', StatusSubmissionEnum::APPROVED)
                ->where('expires_at', '<', now()) // Mengecek izin yang kadaluarsa
                ->with('user') // Eager load relasi user
                ->get();
        
            foreach ($expiredAccess as $access) {
                // Nonaktifkan akses fitur A
                $access->is_active = false;
                $access->save();

                $access->user->access_calc = false; // Karena relasi user sudah dimuat dengan eager loading
                $access->user->save(); // Simpan perubahan pada relasi user
            }
        })->daily(); // Jalankan setiap hari
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
