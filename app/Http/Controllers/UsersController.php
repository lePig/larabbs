<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function __construct()
    {
        // 除了users.show这个路由页面，访问其他页面如果用户未登录都跳转到登录页面
        $this->middleware('auth', ['except' => 'show']);
    }
    /**
     * 个人中心页面
     * @date   2018-03-07
     * @param  User       $user [description]
     * @return [type]           [description]
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 编辑用户资料页面展示
     * @date   2018-03-07
     * @param  User       $user [description]
     * @return [type]           [description]
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * 更新用户资料信息
     * @date   2018-03-07
     * @param  UserRequest        $request  [description]
     * @param  User               $user     [description]
     * @param  ImageUploadHandler $uploader [description]
     * @return [type]                       [description]
     */
    public function update(UserRequest $request, User $user, ImageUploadHandler $uploader)
    {
        $data = $request->all();
        if ($request->avatar) {
            $path = $uploader->save($request->avatar, 'avatars', $user->id, 360); //360为裁剪图片的宽度
            if ($path) {
                $data['avatar'] = $path['path'];
            }
        }
        // dd($data);
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功');        
    }
}
