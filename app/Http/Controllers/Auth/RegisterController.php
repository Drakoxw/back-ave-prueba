<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Tools\Crypter;
use Illuminate\Http\Request;
use App\Services\Validations;
use App\Http\Controllers\ControllerExt;
use Illuminate\Http\Response;

class RegisterController extends ControllerExt
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            Validations::UserRegister($request);

            $user = new User;
            $user->user = $request->input('user');
            $user->password = Crypter::encryptAES($request->input('password'));
            $user->name = $request->input('name');
            $user->rol = 'User';
            $user->save();

            return $this->responseOk(
                ['user' => $request->input('user')],
                'Usuario creado correctamente',
                Response::HTTP_CREATED
            );

        } catch (\Exception $e) {
            return $this->badResponse($e);
        }
    }
}
