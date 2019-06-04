<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => 'create'
        ]);
    }

    /**
     * 显示登陆页面
     */
    public function create()
    {
        return view('sessions.create');
    }

    /*
     * 创建新会话（登录）
     */
    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
           'email'=>'required|email|max:255',
           'password'=>'required'
        ]);

        if(Auth::attempt($credentials, $request->has('remember'))) {
            session()->flash('success', '欢迎回来');
            // return redirect()->route('users.show', [Auth::user()]);
            $fallback = route('users.show', [Auth::user()]);
            return redirect()->intended($fallback);     // 登陆成功后跳转到上一次尝试访问的页面上，为空时跳转到个人中心
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }

    }

    /*
     * 销毁会话（退出登录）
     */
    public function destory()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出');
        return redirect()->route('login');
    }
}
