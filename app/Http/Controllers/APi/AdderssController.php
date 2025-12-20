<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAddressRequest;
use App\Http\Requests\SearchAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Http\Services\Api\AdderssService;
use App\Models\Address;
use Illuminate\Http\Request;

class AdderssController extends Controller
{
    protected $addressservice;
    public function __construct(AdderssService $adderssService)
    {
        $this->addressservice = $adderssService;
    }

    public function index(SearchAddressRequest $request)
    {
        $address = $this->addressservice->showAllAddresses($request->validated());
        return $this->success(AddressResource::collection($address->get()), "Address showed succssefully !", 200);
    }
    public function store(CreateAddressRequest $request)
    {
        $address = $this->addressservice->createAddress($request->validated());
        return $this->success(new AddressResource($address), "Address added succssefully !", 200);
    }
    public function update(UpdateAddressRequest $request)
    {
        $address = $this->addressservice->updateAddress($request->validated());
        return $this->success(new AddressResource($address), "Address updated succssefully !", 200);
    }
    public function destroy($id)
    {
        $this->addressservice->deleteAddress($id);
        return $this->success(null,"Address deleted succssefully !", 200);
    }
}
