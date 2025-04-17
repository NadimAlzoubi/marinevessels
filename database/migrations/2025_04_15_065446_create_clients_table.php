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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('fax', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('trn', 50)->nullable(); // الرقم الضريبي
            $table->text('notes')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone', 30)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active'); // بدلاً من string مفتوح
            $table->string('country')->nullable();
            $table->enum('type', ['individual', 'company'])->nullable(); // اقتراح: نوع العميل
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
