<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('social_media_links', function (Blueprint $table) {
    $table->id();

    $table->foreignId('website_setting_id')
        ->constrained('website_settings')
        ->cascadeOnDelete();

    $table->enum('type', [
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'youtube'
    ]);

    $table->string('link');

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_links');
    }
};