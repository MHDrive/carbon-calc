<?php

use App\Models\Institute;
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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Institute::class)->after('id')->nullable();
            $table->string('number_unique')->nullable()->after('username');
            $table->integer('phone_number')->nullable()->after('email');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('institute_id');
            $table->dropColumn('number_unique');
            $table->dropColumn('phone_number');
        });
    }
};
