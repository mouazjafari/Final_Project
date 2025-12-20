<?php

namespace App\Http\Services\Web\Admin;

use App\Exceptions\GeneralException;
use App\Models\DesignOption;
use Exception;
use Illuminate\Support\Facades\DB;


class DesignOptionService
{
    public function createDesignOption(array $data)
    {
        try {
            DB::beginTransaction();

            $designOption = DesignOption::create([
                'name' => [
                    'ar' => $data['name_ar'],
                    'en' => $data['name_en'],
                ],
                'type' => $data['type'],
            ]);

            DB::commit();
            return $designOption;
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException('فشل في إضافة خيار التصميم: ' . $e->getMessage());
        }
    }
    public function updateDesignOption($id, array $data)
    {
        try {
            DB::beginTransaction();

            $designOption = DesignOption::findOrFail($id);

            $designOption->update([
                'name' => [
                    'ar' => $data['name_ar'],
                    'en' => $data['name_en'],
                ],
                'type' => $data['type'],
            ]);

            DB::commit();
            return $designOption;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل في تحديث خيار التصميم: ' . $e->getMessage());
        }
    }

    // حذف خيار
    public function deleteDesignOption($id)
    {
        try {
            DB::beginTransaction();

            $designOption = DesignOption::findOrFail($id);
            $designOption->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('فشل في حذف خيار التصميم: ' . $e->getMessage());
        }
    }
}
