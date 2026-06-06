<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrowthController extends Controller
{
    // 成长等级配置
    const LEVELS = [
        0 => ['name' => '普通用户', 'min' => 0,    'icon' => '👤'],
        1 => ['name' => '等级1',     'min' => 0,    'icon' => '🥉'],
        2 => ['name' => '等级2',     'min' => 200,  'icon' => '🥈'],
        3 => ['name' => '等级3',     'min' => 600,  'icon' => '🥇'],
        4 => ['name' => '等级4',     'min' => 2000, 'icon' => '🏅'],
        5 => ['name' => '等级5',     'min' => 5000, 'icon' => '🎖️'],
        6 => ['name' => '等级6',     'min' => 15000,'icon' => '👑'],
    ];

    // 每日获取成长值上限
    const DAILY_CAP = 50;

    /**
     * 获取用户成长信息
     */
    public function info(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['success' => false, 'message' => '未登录'], 401);

        $level = $user->growth_level ?? 0;
        $nextLevel = $level + 1;
        $currentMin = self::LEVELS[$level]['min'] ?? 0;
        $nextMin = self::LEVELS[$nextLevel]['min'] ?? null;

        // 今日已获成长值
        $todayEarned = DB::table('growth_logs')
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('amount');

        // 连续签到天数
        $streak = $user->sign_streak ?? 0;

        // 是否已签到
        $signed = $user->last_sign_date && substr($user->last_sign_date, 0, 10) === today()->format('Y-m-d');

        // 签到可获得的成长值
        $signReward = $signed ? 0 : min(10, self::DAILY_CAP - $todayEarned);

        return response()->json([
            'success' => true,
            'data' => [
                'growth_value' => $user->growth_value ?? 0,
                'total_growth' => $user->total_growth ?? 0,
                'growth_level' => $level,
                'level_name' => self::LEVELS[$level]['name'] ?? '普通用户',
                'level_icon' => self::LEVELS[$level]['icon'] ?? '👤',
                'next_level' => $nextLevel <= 6 ? [
                    'level' => $nextLevel,
                    'name' => self::LEVELS[$nextLevel]['name'],
                    'min' => $nextMin,
                    'progress' => $nextMin > 0 ? min(100, round(($user->growth_value - $currentMin) / ($nextMin - $currentMin) * 100)) : 100,
                ] : null,
                'today_earned' => $todayEarned,
                'daily_cap' => self::DAILY_CAP,
                'sign_streak' => $streak,
                'signed_today' => $signed,
                'sign_reward' => $signReward,
                'levels' => collect(self::LEVELS)->map(fn($v, $k) => array_merge($v, ['level' => $k]))->values(),
            ]
        ]);
    }

    /**
     * 每日签到
     */
    public function sign(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['success' => false, 'message' => '未登录'], 401);

        // 检查是否已签到
        if ($user->last_sign_date && substr($user->last_sign_date, 0, 10) === today()->format('Y-m-d')) {
            return response()->json(['success' => false, 'message' => '今日已签到']);
        }

        // 检查每日上限
        $todayEarned = DB::table('growth_logs')
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('amount');

        if ($todayEarned >= self::DAILY_CAP) {
            return response()->json(['success' => false, 'message' => '今日成长值已达上限']);
        }

        // 计算签到奖励（连续签到越多，奖励越高）
        $streak = $user->sign_streak ?? 0;
        $yesterday = today()->subDay()->format('Y-m-d');
        if ($user->last_sign_date && substr($user->last_sign_date, 0, 10) === $yesterday) {
            $streak++;
        } else {
            $streak = 1;
        }

        $baseReward = 10;
        $streakBonus = min($streak, 7); // 连签7天封顶
        $reward = min($baseReward + $streakBonus - 1, self::DAILY_CAP - $todayEarned);

        DB::transaction(function () use ($user, $reward, $streak) {
            $newBalance = ($user->growth_value ?? 0) + $reward;
            $newTotal = ($user->total_growth ?? 0) + $reward;
            $newLevel = $this->calcLevel($newBalance);

            DB::table('users')->where('id', $user->id)->update([
                'growth_value' => $newBalance,
                'total_growth' => $newTotal,
                'growth_level' => $newLevel,
                'last_sign_date' => today(),
                'sign_streak' => $streak,
            ]);

            DB::table('growth_logs')->insert([
                'user_id' => $user->id,
                'amount' => $reward,
                'type' => 'sign',
                'description' => "每日签到(连签{$streak}天)",
                'balance' => $newBalance,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "签到成功 +{$reward}成长值",
            'data' => [
                'reward' => $reward,
                'streak' => $streak,
                'growth_value' => ($user->growth_value ?? 0) + $reward,
                'growth_level' => $this->calcLevel(($user->growth_value ?? 0) + $reward),
            ]
        ]);
    }

    /**
     * 添加成长值（内部调用）
     */
    public static function addGrowth($userId, $amount, $type, $description = '')
    {
        $user = DB::table('users')->where('id', $userId)->first();
        if (!$user) return;

        // 检查每日上限
        $todayEarned = DB::table('growth_logs')
            ->where('user_id', $userId)
            ->whereDate('created_at', today())
            ->sum('amount');

        $actual = min($amount, max(0, self::DAILY_CAP - $todayEarned));
        if ($actual <= 0) return;

        $newBalance = ($user->growth_value ?? 0) + $actual;
        $newTotal = ($user->total_growth ?? 0) + $actual;

        $level = 0;
        foreach (self::LEVELS as $lv => $cfg) {
            if ($newBalance >= $cfg['min']) $level = $lv;
        }

        DB::table('users')->where('id', $userId)->update([
            'growth_value' => $newBalance,
            'total_growth' => $newTotal,
            'growth_level' => $level,
        ]);

        DB::table('growth_logs')->insert([
            'user_id' => $userId,
            'amount' => $actual,
            'type' => $type,
            'description' => $description,
            'balance' => $newBalance,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * 成长值日志
     */
    public function logs(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['success' => false, 'message' => '未登录'], 401);

        $logs = DB::table('growth_logs')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json(['success' => true, 'data' => $logs]);
    }

    private function calcLevel($value)
    {
        $level = 0;
        foreach (self::LEVELS as $lv => $cfg) {
            if ($value >= $cfg['min']) $level = $lv;
        }
        return $level;
    }
}
