<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::with(['user', 'city'])->latest()->get();
        return view('admin.Address', compact('user', 'addresses'));
    }
}
