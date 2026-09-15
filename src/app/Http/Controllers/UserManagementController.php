<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
class UserManagementController extends Controller
{
    // **
    //  * 管理者用画面
    //  * 新しいユーザーを登録する
    //  */
    public function create()
    {
        return view('admin.user-create');
    }

    // **
    //  * 管理者用画面
    //  * 新しいユーザーの登録処理
    //  */
    public function createUser(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = new User();
        $user->createUser($validated);
        return redirect('/admin/user/create')
            ->with('success', '登録が完了しました。');
    }

    // **
    //  * 管理者用画面
    //  * ユーザー一覧画面表示
    //  */
    public function index()
    {
        $users = Role::role();
        return view('admin.user-index', ['users' => $users,]);
    }

    // **
    //  * 管理者用画面
    //  * ユーザー編集画面表示
    //  */
    public function edit(User $user)
    {
        return view('admin.user-edit', ['user' => $user,]);
    }

    // **
    //  * 管理者用画面
    //  * ユーザー編集処理
    //  */
    public function editUser(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        $user->update($validated);
        return view('admin.user-edit', ['user' => $user,]);
    }
}
