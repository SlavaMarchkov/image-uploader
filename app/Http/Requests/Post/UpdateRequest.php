<?php

namespace App\Http\Requests\Post;

class UpdateRequest extends StoreRequest
{
    public function rules()
    {
        $rules = parent::rules();

        $rules['image_ids_for_delete'] = 'nullable|array';
        $rules['image_urls_for_delete'] = 'nullable|array';

        return $rules;
    }
}
