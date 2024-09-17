<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // provinsi
            $table->enum('climate_type', ['dry', 'wet', 'moist'])->nullable(); // jenis iklim
            $table->text('description')->nullable(); // provinsi
            $table->string('province')->nullable(); // provinsi
            $table->string('regency')->nullable(); // kabupaten
            $table->string('subdistrict')->nullable(); // kecamatan
            $table->string('village')->nullable(); // desa
            $table->string('forest_name')->nullable(); // nama hutan
            $table->string('surface_area')->nullable(); // luas area
            $table->string('total_plot')->nullable(); // luas area
            $table->json('meta')->nullable(); // data input
            $table->string('total_biomass')->nullable(); // total biomassa
            $table->string('total_carbon_stocks')->nullable(); // total cafangan karbon
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
