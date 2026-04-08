<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration', function (Blueprint $table) {
            $table->id('id_registration');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_package')->constrained('packages', 'id_package');
            $table->timestamp('registration_date')->useCurrent();
            $table->date('start_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'expired', 'pending', 'cancelled'])->default('pending');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
