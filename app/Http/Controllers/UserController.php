<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Handlers\ImageUploadHandler;
use Auth;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['except' => ['show']]);
    }
    public function show(User $user) {
        return view('users.show', compact('user'));
    }

    public function edit(User $user) {
//        $this->authorize('update', $user);
        if(Auth::user()->cant('update', $user)) {
            return redirect()->route('users.edit', [Auth::user()])->with('danger', '你无权编辑别人的页面~');
        }
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user) {
//        $this->authorize('update', $user);
        if(Auth::user()->cant('update', $user)) {
            return redirect()->route('users.edit', [Auth::user()])->with('danger', '你无权编辑别人的页面~');
        }
        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatar', $user->id, 362);
            if($result) {
                $data['avatar'] = $result['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success','个人资料更新成功！');
    }
}
