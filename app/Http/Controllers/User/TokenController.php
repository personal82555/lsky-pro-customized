<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TokenController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tokens = $user->tokens()
            ->select('id', 'name', 'ip', 'plain_token', 'created_at', 'last_used_at')
            ->orderByDesc('created_at')
            ->paginate(10);
        $totalTokens = $user->tokens()->count();

        return view('user.tokens', compact('tokens', 'totalTokens'));
    }

    public function store(Request $request): Response
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $tokenObj = $user->createToken($request->input('name'));
        $tokenObj->accessToken->ip = $request->ip();
        $tokenObj->accessToken->plain_token = $tokenObj->plainTextToken;
        $tokenObj->accessToken->save();

        return $this->success('Token 创建成功', [
            'token' => $tokenObj->plainTextToken,
            'name' => $request->input('name'),
        ]);
    }

    public function destroy(Request $request, string $id): Response
    {
        $user = Auth::user();
        if ($token = $user->tokens()->find($id)) {
            $token->delete();
            return $this->success('Token 已删除');
        }
        return $this->error('Token 不存在');
    }
}
