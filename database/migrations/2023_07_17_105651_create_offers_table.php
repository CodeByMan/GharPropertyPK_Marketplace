<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('bidder_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->unique(['listing_id', 'bidder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
