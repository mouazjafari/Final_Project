<?php

namespace App\Http\Services\Api;

use App\Models\Design;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DesignService
{
    public function getAllDesigns(array $data)
    {
        // نبدأ بالـ Query Builder
        $query = Design::with(['sizes', 'designOptions', 'images', 'user']);

        // إذا في filters، نطبقها
        if (!empty($data)) {
            $query = $this->filter($query, $data);
        }
        $query->load(['sizes', 'designOptions', 'images', 'user']);
        // نرجع النتائج
        return $query;
    }

    public function getAllMyDesigns(array $data)
    {
        $designs = Design::where('user_id', Auth::id())->get();
        if (!empty($data)) {
            $designs = $this->filter($designs, $data);
        }
        $designs->load(['sizes', 'designOptions', 'images', 'user']);

        return $designs;
    }

    public function filter($query, array $data)
    {
        $query = Design::query();
        if (isset($data['name'])) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        }
        if (isset($data['price'])) {
            $query->where('price', '<=', $data['price']);
        }
        if (isset($data['sizes'])) {
            $sizes = is_array($data['sizes']) ? $data['sizes'] : explode(',', $data['sizes']);
            $query->whereHas('sizes', function ($q) use ($sizes) {
                $q->whereIn('sizes.id', $sizes);
            });
        }
        if (isset($data['user_id'])) {
            $query->where('user_id', $data['user_id']);
        }
        if (isset($data['options'])) {
            $options = is_array($data['options']) ? $data['options'] : explode(',', $data['options']);
            $query->whereHas('designOptions', function ($q) use ($options) {
                $q->whereIn('design_options.id', $options);
            });
        }
        return $query->get();
    }

    public function createDesign(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // معالجة الصورة الرئيسية
                $mainImagePath = null;
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    $mainImagePath = $data['image']->store('designs', 'public');
                }

                // إنشاء التصميم
                $design = Design::create([
                    'user_id' => Auth::id(),
                    'name' => json_encode($data['name']), // تحويل array إلى JSON للتخزين
                    'description' => json_encode($data['description']),
                    'price' => $data['price'],
                    'quantity' => $data['quantity'],
                    'image' => $mainImagePath,
                    0
                ]);

                // ربط الأحجام
                if (!empty($data['sizes'])) {
                    $design->sizes()->sync($data['sizes']);
                }

                // ربط الخيارات
                if (!empty($data['options'])) {
                    $design->designOptions()->sync($data['options']);
                }

                // ربط الصور الإضافية
                if (!empty($data['images'])) {
                    foreach ($data['images'] as $image) {
                        if ($image instanceof UploadedFile) {
                            $relativePath = $image->store('designs', 'public');
                            $design->images()->create([
                                'image_path' => $relativePath,
                            ]);
                        }
                    }
                }

                return $design;
            } catch (\Exception $e) {
                Log::error('Error creating design: ' . $e->getMessage());
                throw $e;
            }
        });
    }
    public function updateDesign(Design $design, array $data)
    {
        return DB::transaction(function () use ($design, $data) {
            try {
                // معالجة الصورة الرئيسية
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    if ($design->image && Storage::disk('public')->exists($design->image)) {
                        Storage::disk('public')->delete($design->image);
                    }
                    $data['image'] = $data['image']->store('designs', 'public');
                } else {
                    unset($data['image']); // نشيلها إذا مش موجودة
                }

                // تحضير البيانات للتحديث (فقط الحقول الموجودة)
                $updateData = array_filter([
                    'name' => isset($data['name']) ? json_encode($data['name']) : null,
                    'description' => isset($data['description']) ? json_encode($data['description']) : null,
                    'price' => $data['price'] ?? null,
                    'quantity' => $data['quantity'] ?? null,
                    'image' => $data['image'] ?? null,
                ], fn($value) => !is_null($value));

                $design->update($updateData);

                // تحديث العلاقات
                if (isset($data['sizes'])) {
                    $design->sizes()->sync($data['sizes']);
                }

                if (isset($data['options'])) {
                    $design->designOptions()->sync($data['options']);
                }

                if (isset($data['images']) && !empty($data['images'])) {
                    foreach ($design->images as $oldImage) {
                        if (Storage::disk('public')->exists($oldImage->image_path)) {
                            Storage::disk('public')->delete($oldImage->image_path);
                        }
                        $oldImage->delete();
                    }

                    foreach ($data['images'] as $image) {
                        if ($image instanceof UploadedFile) {
                            $relativePath = $image->store('designs', 'public');
                            $design->images()->create(['image_path' => $relativePath]);
                        }
                    }
                }
                $design->fresh();
                $design->load(['sizes', 'designOptions', 'images']);
                return $design;
            } catch (\Exception $e) {
                Log::error('Error updating design: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}
