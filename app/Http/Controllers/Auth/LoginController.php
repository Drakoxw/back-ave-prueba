<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\Validations;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerExt;

class LoginController extends ControllerExt
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            Validations::UserLogin($request);

        } catch (\Exception $e) {
            return $this->badResponse($e);
        }
    }
}
