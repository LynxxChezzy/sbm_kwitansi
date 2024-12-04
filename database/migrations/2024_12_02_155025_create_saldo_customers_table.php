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
        Schema::create('saldo_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('tipe_transaksi_id')->constrained('tipe_transaksis')->cascadeOnDelete();
            $table->foreignId('status_follow_up_id')->constrained('status_follow_ups')->cascadeOnDelete();
            $table->string('nomor', 15);
            $table->date('tanggal');
            $table->date('masa');
            $table->string('deskripsi', 50);
            $table->unsignedBigInteger('nilai');
            $table->unsignedBigInteger('nilai_jatuh_tempo');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_customers');
    }
};
