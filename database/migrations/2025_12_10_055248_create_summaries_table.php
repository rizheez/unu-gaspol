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
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->string('prodi_code')->nullable();
            $table->string('prodi_name');
            $table->string('prodi_accred')->nullable();
            $table->string('pt_code')->nullable();
            $table->string('pt_name')->nullable();
            $table->string('jenjang')->nullable();
            $table->integer('total_prodi')->default(0);
            $table->integer('diumumkan_count')->default(0);
            $table->integer('sk_tahap_count')->default(0);
            $table->integer('is_valid_by_univ_count')->default(0);
            $table->json('diumumkan_by_tahap')->nullable();
            $table->json('sk_tahap_by_tahap')->nullable();
            $table->json('status_stats')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};
