<?php
namespace Ohio\Core\Team\Http\Requests;

use Ohio\Core\Base\Http\Requests\FormRequest;

class AttachUser extends FormRequest
{


    public function rules()
    {
        return [
            'id' => 'required|exists:users,id',
        ];
    }

}