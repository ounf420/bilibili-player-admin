<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AuthToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => '未登录',
            ], 401);
        }

        $userId = Cache::get('token_' . $token);
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => '登录已过期',
            ], 401);
        }

        $user = User::find($userId);
        
        if (!$user || $user->status === 0) {
            return response()->json([
                'success' => false,
                'message' => '账号已被禁用',
            ], 403);
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
