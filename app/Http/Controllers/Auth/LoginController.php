<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Tools\AuthJwt;
use App\Tools\Crypter;
use Illuminate\Http\Request;
use App\Services\Validations;
use Illuminate\Http\Response;
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

            $nickname = $request->input('user');
            $password = Crypter::encryptAES($request->input('password'));

            $user = User::where('user', $nickname)->where('password', $password)->first();

            if (!$user) {
                throw new \Exception('Usuario o contraseña incorrectos', Response::HTTP_NOT_FOUND);
            }

            $data = [
                'user'=> $user->user,
                'ds'  => $user->name,
                'id'  => $user->id,
                'rol' => $user->rol
            ];

            $token = AuthJwt::SignInTokenSimple($data);
            return $this->responseOk([ 'token' => $token ]);

        } catch (\Exception $e) {
            return $this->badResponse($e);
        }
    }
}
