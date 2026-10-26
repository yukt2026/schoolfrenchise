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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('subtitle', 255)->nullable();
            $table->string('image_path', 255);
            $table->string('mobile_image_path', 255)->nullable();
            $table->string('link_url', 255)->nullable();
            $table->string('link_target', 20)->default('_self');
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
