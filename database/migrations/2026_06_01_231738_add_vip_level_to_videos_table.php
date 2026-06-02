<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->tinyInteger('vip_level')->default(0)->after('quality')->comment('观看需要VIP等级: 0=免费 1=VIP 2=SVIP');
            $table->boolean('is_recommend')->default(false)->after('vip_level')->comment('是否推荐');
            $table->integer('sort_order')->default(0)->after('is_recommend')->comment('排序权重');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['vip_level','is_recommend','sort_order']);
        });
    }
};
