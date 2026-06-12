<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('location');
            $table->enum('type', ['apartment', 'house', 'land', 'commercial']);
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);
            $table->float('area_sqm');
            $table->enum('status', ['available', 'sold', 'rented'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};