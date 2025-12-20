<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDesignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * تحويل الـ JSON strings إلى arrays قبل الـ validation
     */
    protected function prepareForValidation()
    {
        // تحويل name من JSON string إلى array
        if ($this->has('name') && is_string($this->name)) {
            $this->merge([
                'name' => json_decode($this->name, true)
            ]);
        }

        // تحويل description من JSON string إلى array
        if ($this->has('description') && is_string($this->description)) {
            $this->merge([
                'description' => json_decode($this->description, true)
            ]);
        }

        // تحويل sizes من JSON string إلى array
        if ($this->has('sizes') && is_string($this->sizes)) {
            $this->merge([
                'sizes' => json_decode($this->sizes, true)
            ]);
        }

        // تحويل options من JSON string إلى array
        if ($this->has('options') && is_string($this->options)) {
            $this->merge([
                'options' => json_decode($this->options, true)
            ]);
        }

        // معالجة الصور - تحويلها لـ array إذا موجودة
        if ($this->hasFile('images')) {
            $images = $this->file('images');
            // إذا كانت الصور مش array، حوّلها لـ array
            if (!is_array($images)) {
                $this->files->set('images', [$images]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',

            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.ar' => 'required|string',

            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',

            'sizes' => 'required|array',
            'sizes.*' => 'integer|exists:sizes,id',

            'options' => 'nullable|array',
            'options.*' => 'integer|exists:design_options,id',

            // خلي images optional تماماً
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif'
        ];
    }

    public function messages()
    {
        return [
            // Name messages
            'name.required' => 'اسم التصميم مطلوب',
            'name.array' => 'اسم التصميم يجب أن يكون بصيغة صحيحة',
            'name.en.required' => 'الاسم بالإنجليزية مطلوب',
            'name.ar.required' => 'الاسم بالعربية مطلوب',

            // Description messages
            'description.required' => 'وصف التصميم مطلوب',
            'description.array' => 'وصف التصميم يجب أن يكون بصيغة صحيحة',
            'description.en.required' => 'الوصف بالإنجليزية مطلوب',
            'description.ar.required' => 'الوصف بالعربية مطلوب',

            // Price & Quantity messages
            'price.required' => 'سعر التصميم مطلوب',
            'price.numeric' => 'السعر يجب أن يكون رقماً',
            'price.min' => 'السعر يجب أن يكون أكبر من أو يساوي صفر',

            'quantity.required' => 'الكمية مطلوبة',
            'quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً',
            'quantity.min' => 'الكمية يجب أن تكون أكبر من أو تساوي صفر',

            // Sizes messages
            'sizes.required' => 'يجب اختيار مقاس واحد على الأقل',
            'sizes.array' => 'المقاسات يجب أن تكون بصيغة صحيحة',
            'sizes.*.integer' => 'المقاس يجب أن يكون رقماً صحيحاً',
            'sizes.*.exists' => 'أحد المقاسات المختارة غير صالح',

            // Options messages
            'options.array' => 'الخيارات يجب أن تكون بصيغة صحيحة',
            'options.*.integer' => 'الخيار يجب أن يكون رقماً صحيحاً',
            'options.*.exists' => 'أحد الخيارات المختارة غير صالح',

            // Images messages
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'الصورة يجب أن تكون من نوع: jpeg, png, jpg, gif',

            'images.array' => 'الصور يجب أن تكون بصيغة صحيحة',
            'images.*.image' => 'كل الملفات يجب أن تكون صور',
            'images.*.mimes' => 'الصور يجب أن تكون من نوع: jpeg, png, jpg, gif',
        ];
    }
}
