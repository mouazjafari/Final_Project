<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Enum\RoleUserEnum;
use App\Models\Address;
use App\Models\Design;
use App\Models\DesignOption;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $usersCount = User::Role(RoleUserEnum::User->value)->count();
        $addressCount = Address::count();
        $designCount = Design::count();
        $designOptionCount = DesignOption::count();
        $orderCount = Order::count();
        return view('admin.dashboard', compact('user', 'usersCount', 'addressCount', 'designCount', 'designOptionCount', 'orderCount'));
    }
}
