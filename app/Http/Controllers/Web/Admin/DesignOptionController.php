<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\designOptionStoreRequest;
use App\Http\Services\Web\Admin\DesignOptionService;
use App\Models\DesignOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DesignOptionController extends Controller
{
    protected $designOptionService;

    public function __construct(DesignOptionService $designOptionService)
    {
        $this->designOptionService = $designOptionService;
    }
    public function index()
    {
        $designOptions = DesignOption::get();
        return view('admin.design_options', compact('designOptions'));
    }
    public function store(designOptionStoreRequest $request)
    {
        $user = Auth::user();
        Gate::authorize('create', DesignOption::class);
        $this->designOptionService->createDesignOption($request->validated());
        return redirect()->route('admin.design_options')
            ->with('success', 'تم إضافة خيار التصميم بنجاح! ✅');
    }
    public function update(designOptionStoreRequest $request, $id)
    {
        $designOption = DesignOption::findOrFail($id);

        // ⚠️ مهم: مرر الـ instance مش الـ class
        Gate::authorize('update', $designOption);

        $this->designOptionService->updateDesignOption($id, $request->validated());

        return redirect()->route('admin.design_options')
            ->with('success', 'تم تحديث خيار التصميم بنجاح! ✅');
    }

    public function destroy($id)
    {
        $designOption = DesignOption::findOrFail($id);

        // ⚠️ مهم: مرر الـ instance مش الـ class
        Gate::authorize('delete', $designOption);

        $this->designOptionService->deleteDesignOption($id);

        return redirect()->route('admin.design_options')
            ->with('success', 'تم حذف خيار التصميم بنجاح! ✅');
    }
}
