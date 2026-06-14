<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->string('player_code', 10)->nullable()->after('slug')->unique()->comment('播放器ID：10位随机数字');
        });

        // 给现有播放器生成 player_code
        $players = DB::table('user_players')->whereNull('player_code')->get();
        foreach ($players as $player) {
            $code = self::generateCode();
            DB::table('user_players')->where('id', $player->id)->update(['player_code' => $code]);
        }

        // 将 player_key 改为32位MD5格式（现有数据保留，新创建的用MD5）
    }

    public function down(): void
    {
        Schema::table('user_players', function (Blueprint $table) {
            $table->dropColumn('player_code');
        });
    }

    private static function generateCode(): string
    {
        $code = '';
        for ($i = 0; $i < 10; $i++) {
            $code .= mt_rand(0, 9);
        }
        // 确保不重复
        $exists = DB::table('user_players')->where('player_code', $code)->exists();
        if ($exists) {
            return self::generateCode();
        }
        return $code;
    }
};
