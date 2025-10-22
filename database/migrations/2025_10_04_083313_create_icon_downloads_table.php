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
        Schema::create('icon_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('icon_id')->constrained()->onDelete('cascade');
            $table->foreignId('icon_file_id')->constrained()->onDelete('cascade');
            $table->enum('download_type', ['svg', 'png']);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('downloaded_at')->useCurrent();

            $table->index('user_id');
            $table->index('icon_id');
            $table->index('downloaded_at');
            $table->index(['icon_id', 'download_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icon_downloads');
    }
};
