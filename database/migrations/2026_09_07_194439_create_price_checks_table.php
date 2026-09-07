<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_alert_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3);
            $table->string('carrier_code')->nullable();
            $table->json('raw_offer')->nullable();
            $table->timestamps();

            $table->index(['flight_alert_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_checks');
    }
};
