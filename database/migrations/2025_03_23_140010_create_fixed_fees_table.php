<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixedFeesTable extends Migration
{
    public function up()
    {
        Schema::create('fixed_fees', function (Blueprint $table) {
            $table->id();
            $table->string('fee_name');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_rate', 5, 4)->default(0); // 0.0500 for 5%
            $table->foreignId('fee_category_id')->constrained('fee_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fixed_fees');
    }
}
