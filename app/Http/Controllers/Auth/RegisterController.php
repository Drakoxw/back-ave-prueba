<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ControllerExt;
use App\Services\Validations;
use Illuminate\Http\Request;

class RegisterController extends ControllerExt
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            Validations::UserRegister($request);


        } catch (\Exception $e) {
            return $this->badResponse($e);
        }
    }
}
