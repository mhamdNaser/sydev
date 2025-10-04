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
        Schema::create('icon_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('icon_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size')->default(0);
            $table->enum('file_type', ['svg', 'png']);
            $table->string('dimensions')->nullable(); // مثل '16x16', '32x32' لملفات PNG
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icon_files');
    }
};
