<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('email_verified_at'); // ✅ إضافة عمود يوزر نيم فريد
            $table->string('role')->default('guest')->after('password'); // ✅ إضافة العمود
            $table->boolean('active')->default(true)->after('password'); // ✅ إضافة عمود active مع القيم الافتراضية
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username'); // ✅ التراجع في حال rollback
            $table->dropColumn('role'); // ✅ حذف العمود عند التراجع
            $table->dropColumn('active'); // ✅ التراجع في حال rollback
        });
    }
};