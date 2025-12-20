<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $data = [];

        // تحويل name من JSON string إلى array
        if ($this->has('name')) {
            $name = $this->input('name');
            $data['name'] = is_string($name) ? json_decode($name, true) : $name;
        }

        // تحويل description من JSON string إلى array
        if ($this->has('description')) {
            $description = $this->input('description');
            $data['description'] = is_string($description) ? json_decode($description, true) : $description;
        }

        // تحويل sizes من JSON string إلى array
        if ($this->has('sizes')) {
            $sizes = $this->input('sizes');
            $data['sizes'] = is_string($sizes) ? json_decode($sizes, true) : $sizes;
        }

        // تحويل options من JSON string إلى array
        if ($this->has('options')) {
            $options = $this->input('options');
            $data['options'] = is_string($options) ? json_decode($options, true) : $options;
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        return [
            // الصورة الرئيسية
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',

            // الاسم
            'name' => 'sometimes|array',
            'name.en' => 'sometimes|string|max:255',
            'name.ar' => 'sometimes|string|max:255',

            // الوصف
            'description' => 'sometimes|array',
            'description.en' => 'sometimes|string',
            'description.ar' => 'sometimes|string',

            // السعر والكمية
            'price' => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:0',

            // الأحجام
            'sizes' => 'sometimes|array',
            'sizes.*' => 'integer|exists:sizes,id',

            // الخيارات
            'options' => 'sometimes|array',
            'options.*' => 'integer|exists:design_options,id',

            // الصور الإضافية
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }
}
