<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Tools\Crypter;
use Illuminate\Http\Request;
use App\Services\Validations;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerExt;

class RegisterUserAdminController extends ControllerExt
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $token = $this->Authorization();
            if ($token['rol'] != 'SuperAdmin') {
                throw new \Exception('No tienes permisos para realizar esta acción', Response::HTTP_UNAUTHORIZED);
            }

            Validations::UserRegister($request);

            $user = new User;
            $user->user = $request->input('user');
            $user->password = Crypter::encryptAES($request->input('password'));
            $user->name = $request->input('name');
            $user->rol = 'Admin';
            $user->save();

            return $this->responseOk(
                ['user' => $request->input('user')],
                'Usuario administrador creado correctamente',
                Response::HTTP_CREATED
            );

        } catch (\Exception $e) {
            return $this->badResponse($e);
        }
    }
}
