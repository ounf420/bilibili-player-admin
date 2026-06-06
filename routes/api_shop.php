<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlayerShopController;

// 播放器商城API（需要登录）
Route::middleware('auth:sanctum')->prefix('shop')->group(function () {
    // 获取套餐列表
    Route::get('/plans', [PlayerShopController::class, 'plans']);
    
    // 获取用户商城信息（版本+额度+订单）
    Route::get('/my', [PlayerShopController::class, 'myInfo']);
    
    // 卡密兑换
    Route::post('/redeem', [PlayerShopController::class, 'redeemCard']);
    
    // 创建订单（支付宝/微信）
    Route::post('/orders', [PlayerShopController::class, 'createOrder']);
    
    // 查询订单状态
    Route::get('/orders/{orderNo}/status', [PlayerShopController::class, 'orderStatus']);
});
