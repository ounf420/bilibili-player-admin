<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('growth_value')->default(0)->after('vip_expire_at')->comment('成长值');
            $table->tinyInteger('growth_level')->default(0)->after('growth_value')->comment('成长等级0-6');
            $table->integer('total_growth')->default(0)->after('growth_level')->comment('累计成长值');
            $table->date('last_sign_date')->nullable()->after('total_growth')->comment('最后签到日期');
            $table->integer('sign_streak')->default(0)->after('last_sign_date')->comment('连续签到天数');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['growth_value','growth_level','total_growth','last_sign_date','sign_streak']);
        });
    }
};
