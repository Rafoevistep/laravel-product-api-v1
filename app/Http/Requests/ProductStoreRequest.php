<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
        public function authorize()
        {
            //return false;
            return true;
        }

        public function rules()
        {
            if(request()->isMethod('product')) {
                return [
                    'name' => 'required|string|max:258',
                    'price' => 'string|min:1',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'description' => 'required|string'
                ];
            } else {
                return [
                    'name' => 'required|string|max:258',
                    'price' => 'string|min:1',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'description' => 'required|string'
                ];
            }
        }

        public function messages()
        {
            if(request()->isMethod('product')) {
                return [
                    'name.required' => 'Name is required!',
                    'image.required' => 'Image is required!',
                    'description.required' => 'Descritpion is required!'
                ];
            } else {
                return [
                    'name.required' => 'Name is required!',
                    'description.required' => 'Descritpion is required!'
                ];
            }
        }
    }
