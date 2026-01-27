<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Services\Web\Admin\PermissionService;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        // Gate::authorize('viewAny', Permission::class);

        $permissions = $this->permissionService->getAllPermissions();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function store(CreatePermissionRequest $request)
    {
        // Gate::authorize('create', Permission::class);

        try {
            $this->permissionService->createPermission($request->validated());

            return redirect()
                ->route('admin.permissions.index')
                ->with('success', '✅ تم إنشاء الصلاحية بنجاح!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // Gate::authorize('delete', Permission::class);

        try {
            $permission = Permission::findOrFail($id);
            $this->permissionService->deletePermission($permission);

            return redirect()
                ->route('admin.permissions.index')
                ->with('success', '✅ تم حذف الصلاحية بنجاح!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }
}
