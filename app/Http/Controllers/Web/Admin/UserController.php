<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Enum\RoleUserEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $users = User::Role(RoleUserEnum::User->value)->get();
        return view('admin.users', compact('user', 'users'));
    }
}
