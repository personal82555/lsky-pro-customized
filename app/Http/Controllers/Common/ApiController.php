<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiController extends Controller
{
    public function index(Request $request): View
    {
        $tokens = [];
        $totalTokens = 0;
        $newToken = null;
        $albums = Album::select('id', 'name')->orderBy('id')->get();

        if ($request->user()) {
            $totalTokens = $request->user()->tokens()->count();
            $tokens = $request->user()->tokens()
                ->select('id', 'name', 'ip', 'plain_token', 'created_at', 'last_used_at')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        // 处理新建 token
        if ($request->isMethod('post') && $request->user()) {
            $name = $request->input('token_name', 'api-token');
            $tokenObj = $request->user()->createToken($name);
            $tokenObj->accessToken->ip = $request->ip();
            $tokenObj->accessToken->plain_token = $tokenObj->plainTextToken;
            $tokenObj->accessToken->save();
            $newToken = $tokenObj->plainTextToken;
            $totalTokens = $request->user()->tokens()->count();
            $tokens = $request->user()->tokens()
                ->select('id', 'name', 'ip', 'plain_token', 'created_at', 'last_used_at')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return view('common.api', compact('tokens', 'totalTokens', 'newToken', 'albums'));
    }
}
