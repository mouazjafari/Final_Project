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
        $newUser->roles()->attach($selectedRole->id, [
            'model_type' => 'App\Models\User'
        ]);
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

        // التحقق من البيرمشنز
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
        if (!$currentUser->hasPermissionTo('create users', 'web')) {
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

        // حذف كل الأدوار من كل الـ guards
        \DB::table('model_has_roles')->where('model_id', $user->id)
            ->where('model_type', 'App\Models\User')->delete();

        // إعطاء الدور الجديد لـ web guard مباشرة
        $user->roles()->attach($selectedRole->id, [
            'model_type' => 'App\Models\User'
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'تم تحديث دور المستخدم بنجاح!');
    }

    /**
     * تفعيل/تعطيل المستخدم
     */
    public function toggleStatus($id)
    {
        $currentUser = Auth::user();

        // التحقق من الصلاحيات
        if (!$currentUser->hasPermissionTo('create users', 'web')) {
            return redirect()->route('admin.users')
                ->with('error', 'ليس لديك صلاحية لتعديل المستخدمين!');
        }

        $user = User::findOrFail($id);

        // التحقق من أن المستخدم لديه دور user فقط
        if ($user->roles->isEmpty() || $user->roles->first()->name !== RoleUserEnum::User->value) {
            return redirect()->route('admin.users')
                ->with('error', 'يمكن تغيير حالة المستخدمين الذين لديهم دور user فقط!');
        }

        // منع تعطيل الحساب الشخصي
        if ($user->id === $currentUser->id) {
            return redirect()->route('admin.users')
                ->with('error', 'لا يمكنك تعطيل حسابك الشخصي!');
        }

        // تبديل الحالة
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'تم تفعيل' : 'تم تعطيل';
        return redirect()->route('admin.users')
            ->with('success', $status . ' المستخدم بنجاح!');
    }
}
