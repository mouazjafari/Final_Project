<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    /**
     * عرض جميع الكوبونات
     */
    public function index()
    {
        // Gate::authorize('view', Coupon::class);

        $coupons = Coupon::withCount(['usages', 'allowedUsers'])
            ->latest()
            ->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * صفحة إنشاء كوبون جديد
     */
    public function create()
    {
        // Gate::authorize('create', Coupon::class);

        $users = User::role('user')->select('id', 'name', 'email')->get();

        return view('admin.coupons.create', compact('users'));
    }

    /**
     * حفظ كوبون جديد
     */
    public function store(CreateCouponRequest $request)
    {
        // Gate::authorize('create', Coupon::class);

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // إنشاء الكوبون
            $coupon = Coupon::create([
                'code' => $data['code'],
                'type' => $data['type'],
                'amount' => $data['amount'],
                'max_uses' => $data['max_uses'] ?? null,
                'starts_at' => $data['starts_at'] ?? now(),
                'expires_at' => $data['expires_at'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            // ربط المستخدمين المسموح لهم (إذا موجودين)
            if (!empty($data['allowed_users'])) {
                $coupon->allowedUsers()->sync($data['allowed_users']);
            }

            DB::commit();

            return redirect()
                ->route('admin.coupons.index')
                ->with('success', '✅ تم إنشاء الكوبون بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating coupon: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', '❌ حدث خطأ أثناء إنشاء الكوبون');
        }
    }

    /**
     * صفحة تعديل كوبون
     */
    public function edit($id)
    {
        // Gate::authorize('update', Coupon::class);

        $coupon = Coupon::with('allowedUsers')->findOrFail($id);
        $users = User::role('user')->select('id', 'name', 'email')->get();

        return view('admin.coupons.edit', compact('coupon', 'users'));
    }

    /**
     * تحديث كوبون
     */
    public function update(UpdateCouponRequest $request, $id)
    {
        // Gate::authorize('update', Coupon::class);

        try {
            DB::beginTransaction();

            $coupon = Coupon::findOrFail($id);
            $data = $request->validated();

            // تحديث البيانات
            $coupon->update($data);

            // تحديث المستخدمين المسموح لهم
            if (isset($data['allowed_users'])) {
                $coupon->allowedUsers()->sync($data['allowed_users']);
            }

            DB::commit();

            return redirect()
                ->route('admin.coupons.index')
                ->with('success', '✅ تم تحديث الكوبون بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating coupon: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', '❌ حدث خطأ أثناء تحديث الكوبون');
        }
    }

    /**
     * حذف كوبون
     */
    public function destroy($id)
    {
        // Gate::authorize('delete', Coupon::class);

        try {
            $coupon = Coupon::findOrFail($id);

            // التحقق من عدم استخدامه
            if ($coupon->used_count > 0) {
                return back()->with('error', '❌ لا يمكن حذف كوبون تم استخدامه');
            }

            $coupon->delete();

            return redirect()
                ->route('admin.coupons.index')
                ->with('success', '✅ تم حذف الكوبون بنجاح!');

        } catch (\Exception $e) {
            Log::error('Error deleting coupon: ' . $e->getMessage());

            return back()->with('error', '❌ حدث خطأ أثناء حذف الكوبون');
        }
    }

    /**
     * تفعيل/تعطيل كوبون
     */
    public function toggleStatus($id)
    {
        // Gate::authorize('update', Coupon::class);

        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->update(['is_active' => !$coupon->is_active]);

            $status = $coupon->is_active ? 'مفعل' : 'معطل';

            return response()->json([
                'success' => true,
                'message' => "تم تحديث حالة الكوبون إلى: {$status}",
                'is_active' => $coupon->is_active,
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling coupon status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة',
            ], 500);
        }
    }

    /**
     * عرض تفاصيل استخدامات الكوبون
     */
    public function showUsages($id)
    {
        // Gate::authorize('view', Coupon::class);

        $coupon = Coupon::with(['usages.user', 'usages.order'])
            ->findOrFail($id);

        return view('admin.coupons.usages', compact('coupon'));
    }
}
