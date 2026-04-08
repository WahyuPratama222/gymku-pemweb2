<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id_payment');
            $table->foreignId('id_registration')->constrained('registration', 'id_registration')->onDelete('cascade');
            $table->enum('payment_method', ['Transfer Bank', 'Tunai', 'QRIS', 'E-Wallet']);
            $table->enum('payment_status', ['Lunas', 'Belum Lunas', 'Gagal'])->default('Belum Lunas');
            $table->timestamp('payment_date')->useCurrent();
            $table->decimal('amount', 12, 0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
