<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Services\Web\Admin\RoleService;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        Gate::authorize('viewAny', Role::class);

        $roles = $this->roleService->getAllRoles();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        Gate::authorize('create', Role::class);

        // عرض صلاحيات الـ web فقط
        $permissions = Permission::where('guard_name', 'web')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(CreateRoleRequest $request)
    {
        Gate::authorize('create', Role::class);

        try {
            $this->roleService->createRole($request->validated());

            return redirect()
                ->route('admin.roles.index')
                ->with('success', '✅ تم إنشاء الدور بنجاح!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        Gate::authorize('update', Role::class);

        $role = Role::with('permissions')->findOrFail($id);
        // عرض صلاحيات الـ web فقط
        $permissions = Permission::where('guard_name', 'web')->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        Gate::authorize('update', Role::class);

        try {
            $role = Role::findOrFail($id);
            $this->roleService->updateRole($role, $request->validated());

            return redirect()
                ->route('admin.roles.index')
                ->with('success', '✅ تم تحديث الدور بنجاح!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Gate::authorize('delete', Role::class);

        try {
            $role = Role::findOrFail($id);
            $this->roleService->deleteRole($role);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', '✅ تم حذف الدور بنجاح!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }
}
