<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceFeesTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('fixed_fee_id')->constrained('fixed_fees')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('discount', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_fees');
    }
}
