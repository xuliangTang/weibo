<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
           'except' => ['show', 'create', 'store', 'index']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * 用户列表页
     */
    public function index()
    {
        // $users = User::all();
        $users = User::paginate(8);
        return view('users.index', compact('users'));
    }

    /**
     * 用户注册页面
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 显示用户个人信息的页面
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 创建用户
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }

    /**
     *  编辑用户个人资料的页面
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);  // 参数一为授权策略的名称，参数二为进行授权验证的数据
        return view('users.edit', ['user'=>$user]);
    }

    /**
     *  更新用户
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);  // 参数一为授权策略的名称，参数二为进行授权验证的数据
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if(isset($request->password) && $request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash('success', '更新资料成功');
        return redirect()->route('users.show', $user->id);
    }

    /**
     * 删除用户
     */
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);     // 控制器授权策略
        $user->delete();
        session()->flash('success', '成功删除用户');
        return back();
    }
}
