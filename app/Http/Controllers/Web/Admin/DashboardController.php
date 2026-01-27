<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Enum\RoleUserEnum;
use App\Models\Address;
use App\Models\Coupon;
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
        $usersCount = User::Role(RoleUserEnum::User->value)->count();
        $addressCount = Address::count();
        $designCount = Design::count();
        $designOptionCount = DesignOption::count();
        $orderCount = Order::count();
        $couponCount = Coupon::count();

        // ✅ جديد
        $rolesCount = \Spatie\Permission\Models\Role::count();
        $permissionsCount = \Spatie\Permission\Models\Permission::count();

        return view('admin.dashboard', compact(
            'user',
            'usersCount',
            'addressCount',
            'designCount',
            'designOptionCount',
            'orderCount',
            'couponCount',
            'rolesCount',
            'permissionsCount'
        ));
    }
}
