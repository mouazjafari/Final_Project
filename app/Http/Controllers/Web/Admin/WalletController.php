<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletRequest;
use App\Http\Services\Web\Admin\WalletService;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * عرض جميع المحافظ
     */
    public function index()
    {
        $wallets = $this->walletService->getAllWallets();
        return view('admin.wallets.index', compact('wallets'));
    }

    /**
     * عرض تفاصيل محفظة مستخدم
     */
    public function show($userId)
    {
        try {
            $data = $this->walletService->getUserWalletDetails($userId);
            return view('admin.wallets.show', $data);
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    /**
     * إضافة رصيد
     */
    public function addBalance(WalletRequest $request, $userId)
    {

        Gate::authorize('add',Wallet::class);

        try {
            $this->walletService->addBalance(
                $userId,
                $request->validated(),
            );

            return back()->with('success', '✅ تم إضافة المبلغ بنجاح!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * سحب رصيد
     */
    public function withdrawBalance(WalletRequest $request, $userId)
    {
        Gate::authorize('withdraw',  Wallet::class);
        try {
            $this->walletService->withdrawBalance(
                $userId,
                $request->validated()

            );

            return back()->with('success', '✅ تم سحب المبلغ بنجاح!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }
}
