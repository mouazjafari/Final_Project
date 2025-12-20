<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // البيانات الأساسية
            'name' => $this->getTranslationsFromDesign('name'),
            'description' => $this->getTranslationsFromDesign('description'),
            'price' => $this->price,
            'quantity' => $this->quantity,

            // sizes - ✅ راح يرجع [] إذا مش محملة
            'sizes' => $this->relationLoaded('sizes')
                ? $this->sizes->map(function ($size) {
                    return [
                        'id' => $size->id,
                        'name' => $size->name,
                    ];
                })
                : [],

            // images - ✅ راح يرجع [] إذا مش محملة
            'images' => $this->relationLoaded('images')
                ? $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'name' => $image->image_path,
                    ];
                })
                : [],

            // design options
            'design_options' => $this->relationLoaded('designOptions')
                ? $this->designOptions->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'name' => $this->getTranslationsFromModel($option, 'name'),
                        'type' => $option->type
                    ];
                })
                : [],

            // user
            'user_name' => $this->relationLoaded('user') ? $this->user?->name : null,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function getTranslationsFromModel($model, string $attribute): array
    {
        if (!$model) {
            return [];
        }

        if (method_exists($model, 'getTranslations')) {
            return $model->getTranslations($attribute) ?? [];
        }

        if (isset($model->{$attribute})) {
            if (is_string($model->{$attribute})) {
                $decoded = json_decode($model->{$attribute}, true);
                return is_array($decoded) ? $decoded : [];
            }

            if (is_array($model->{$attribute})) {
                return $model->{$attribute};
            }
        }

        return [];
    }

    private function getTranslationsFromDesign(string $attribute): array
    {
        if (!isset($this->{$attribute})) {
            return [];
        }

        if (method_exists($this->resource, 'getTranslations')) {
            return $this->resource->getTranslations($attribute) ?? [];
        }

        if (is_string($this->{$attribute})) {
            $decoded = json_decode($this->{$attribute}, true);
            return is_array($decoded) ? $decoded : [];
        }

        if (is_array($this->{$attribute})) {
            return $this->{$attribute};
        }

        return [];
    }
}
