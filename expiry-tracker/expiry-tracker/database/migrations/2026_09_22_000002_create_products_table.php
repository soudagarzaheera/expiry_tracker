<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('batch_number');
            $table->unsignedInteger('quantity');
            $table->string('unit', 40);
            $table->date('manufacturing_date');
            $table->date('expiry_date');
            $table->text('description')->nullable();
            $table->string('disposal_status')->default('active');
            $table->timestamp('disposed_at')->nullable();
            $table->timestamp('alert_dismissed_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'expiry_date']);
            $table->index('disposal_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
