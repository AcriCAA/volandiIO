<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('origin', 3);
            $table->string('destination', 3);
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->unsignedTinyInteger('adults')->default(1);
            $table->string('cabin_class')->default('ECONOMY');
            $table->decimal('target_price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('last_price', 10, 2)->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('triggered_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'departure_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_alerts');
    }
};
