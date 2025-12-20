<?php

namespace App\Http\Services\Api;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdderssService
{
    public function showAllAddresses(array $data)
    {
        $query = Address::where('user_id', Auth::id());

        if (!empty($data['search_street'])) {
            $query->where('street', 'like', '%' . $data['search_street'] . '%');
        } else if (!empty($data['search_area'])) {
            $query->where('area', 'like', '%' . $data['search_area'] . '%');
        }

        if (!empty($data['Longitude']) && !empty($data['Langitude'])) {
            $query->where('Longitude', $data['Longitude'])->where('Langitude', $data['Langitude']);
        }

        if (!empty($data['filter_by'])) {
            $query->orderBy('created_at', $data['filter_by']);
        }


        if (!empty($data['sort_city_id'])) {
            $query->where('city_id', $data['sort_city_id']);
        } else if (!empty($data['sort_street'])) {
            $query->where('street', $data['sort_street']);
        } else if (!empty($data['sort_area'])) {
            $query->where('area', $data['sort_area']);
        }

        if (!empty($data['pagination'])) {
            return $query->paginate($data['pagination']);
        }
        $query->get();
        return $query;
    }
    public function createAddress(array $data)
    {
        Gate::authorize('create', Address::class);
        $data['user_id'] = Auth::id();
        $address = Address::create($data);
        return $address;
    }
    public function updateAddress(array $data)
    {
        $address = Address::findorfail($data['address_id']);
        Gate::authorize('update', $address);
        $address->update($data);
        return $address;
    }
    public function deleteAddress($id)
    {
        Gate::authorize('delete', Address::findorfail($id));
        Address::destroy($id);
        return true;
    }
}
