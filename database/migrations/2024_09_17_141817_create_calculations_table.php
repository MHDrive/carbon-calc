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
            $table->enum('climate_type', ['dry', 'wet', 'moist'])->nullable(); // jenis iklim
            $table->string('provinces')->nullable(); // provinsi
            $table->string('regencies')->nullable(); // kabupaten
            $table->string('districts')->nullable(); // kecamatan
            $table->string('villages')->nullable(); // desa
            $table->string('forest_name')->nullable(); // nama hutan
            $table->string('surface_area')->nullable(); // luas area
            $table->string('surface_area_unit')->nullable(); // luas area perunit
            $table->enum('method', ['sampling', 'sensus'])->nullable(); // luas area
            $table->json('meta')->nullable(); // data input
            $table->string('total_biomass')->nullable(); // total biomassa
            // $table->string('total_biomass_unit')->nullable(); // total biomassa
            $table->string('total_carbon_stocks')->nullable(); // total cafangan karbon
            // $table->string('total_carbon_stocks_unit')->nullable(); // total cafangan karbon
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
