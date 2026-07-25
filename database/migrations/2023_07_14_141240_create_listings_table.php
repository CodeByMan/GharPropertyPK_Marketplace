<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('beds');
            $table->unsignedTinyInteger('baths');
            $table->unsignedSmallInteger('area');
            $table->string('city', 100);
            $table->string('code', 20);
            $table->string('street', 150);
            $table->string('street_nr', 20);
            $table->unsignedInteger('price');
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
