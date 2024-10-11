<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class); // user_id
            $table->text('notes')->nullable(); // keterangan / catatan admin untuk memberitahukan ke user.
            $table->boolean('is_active')->default(true); // 
            $table->timestamp('granted_at')->nullable(); // Tanggal izin diberikan
            $table->timestamp('expires_at')->nullable(); // Tanggal izin berakhir
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
