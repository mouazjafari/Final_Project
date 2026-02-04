<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Enum\RoleUserEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $users = User::with('roles')->where('id', '!=', $user->id)->get();
        return view('admin.users', compact('user', 'users'));
    }

    /**
     * عرض صفحة إنشاء مستخدم جديد
     */
    public function create()
    {
        $user = Auth::user();

        // التحقق من صلاحية إنشاء مستخدمين
        if (!$user->hasPermissionTo('create users', 'web')) {
            return redirect()->route('admin.users')
                ->with('error', 'ليس لديك صلاحية لإنشاء مستخدمين!');
        }

        // جلب كل الأدوار ما عدا SuperAdmin
        $availableRoles = Role::where('guard_name', 'web')
            ->where('name', '!=', RoleUserEnum::SuperAdmin->value)
            ->get();

        return view('admin.users.create', compact('availableRoles'));
    }

    /**
     * حفظ المستخدم الجديد
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // التحقق من الصلاحيات
        if (!$currentUser->hasPermissionTo('create users', 'web')) {
            return redirect()->route('admin.users')
                ->with('error', 'ليس لديك صلاحية لإنشاء مستخدمين!');
        }

        // التحقق من البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
            'role_id.required' => 'الدور مطلوب',
            'role_id.exists' => 'الدور المحدد غير موجود',
        ]);

        // التحقق من أن الدور المُختار ليس SuperAdmin
        $selectedRole = Role::find($validated['role_id']);

        if ($selectedRole->name === RoleUserEnum::SuperAdmin->value) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'لا يمكنك إعطاء دور SuperAdmin! يمكنك فقط إعطاء الأدوار الأخرى.');
        }

        // إنشاء المستخدم
        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
        ]);

        // إعطاء الدور للمستخدم الجديد
        $newUser->assignRole($selectedRole->name);

        return redirect()->route('admin.users')
            ->with('success', 'تم إنشاء المستخدم بنجاح!');
    }

    /**
     * عرض صفحة تعديل دور المستخدم
     */
    public function edit($id)
    {
        $currentUser = Auth::user();
        $user = User::with('roles')->findOrFail($id);

        // التحقق من c
        if (!$currentUser->hasPermissionTo('create users', 'web')) {
            return redirect()->route('admin.users')
                ->with('error', 'ليس لديك صلاحية لتعديل أدوار المستخدمين!');
        }

        // جلب كل الأدوار ما عدا SuperAdmin
        $availableRoles = Role::where('guard_name', 'web')
            ->where('name', '!=', RoleUserEnum::SuperAdmin->value)
            ->get();

        return view('admin.users.edit', compact('user', 'availableRoles'));
    }

    /**
     * تحديث دور المستخدم
     */
    public function updateRole(Request $request, $id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);

        // التحقق من الصلاحية
        if (!$currentUser->hasPermissionTo('edit users', 'web')) {
            return redirect()->route('admin.users')
                ->with('error', 'ليس لديك صلاحية لتعديل أدوار المستخدمين!');
        }

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ], [
            'role_id.required' => 'الدور مطلوب',
            'role_id.exists' => 'الدور المحدد غير موجود',
        ]);

        // التحقق من أن الدور المُختار ليس SuperAdmin
        $selectedRole = Role::find($validated['role_id']);

        if ($selectedRole->name === RoleUserEnum::SuperAdmin->value) {
            return redirect()->back()
                ->with('error', 'لا يمكنك إعطاء دور SuperAdmin!');
        }

        // تحديث الدور
        $user->syncRoles([$selectedRole->name]);

        return redirect()->route('admin.users')
            ->with('success', 'تم تحديث دور المستخدم بنجاح!');
    }
}
