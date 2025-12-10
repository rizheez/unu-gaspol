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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('no_kk')->nullable();
            $table->string('nim')->nullable();
            $table->string('name');
            $table->boolean('is_nik_valid')->nullable();
            $table->boolean('is_valid_by_univ')->nullable();
            $table->string('status')->nullable();
            $table->text('status_desc')->nullable();
            $table->integer('status_id')->nullable();
            $table->string('pt_name')->nullable();
            $table->string('prodi_name')->nullable();
            $table->string('jenjang')->nullable();
            $table->string('jalur_masuk')->nullable();
            $table->decimal('max_spp', 15, 2)->nullable();
            $table->decimal('spp_ditanggung', 15, 2)->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('batch_accepted')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
