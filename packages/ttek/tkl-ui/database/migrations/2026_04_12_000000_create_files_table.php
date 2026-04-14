<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('fkey', 191);
            $table->unsignedBigInteger('fid');
            $table->string('original_name');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fkey', 'fid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
