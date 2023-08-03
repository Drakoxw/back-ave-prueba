<?php

namespace App\Traits;

use Exception;
use App\Tools\AuthJwt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Classes\Entities\AuthorizationEntity;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

trait HeadersTrait
{

    /** se requiere q llame a $this->Authorization() */
    public string $token;

    /**
     * Accede al token para retornar los datos del token procesados y el token string hacerlo publico desde el controlador
     *  * Da acceso a $this->token
     *
     * @param Request $request [explicite description]
     *
     * @return AuthorizationEntity
     * @throws \Exception
     */
    public function Authorization(Request $request = null): array
    {
        if ($request == null) {
            $request = request();
        }

        $token = $request->header('Authorization')
            ?? $request->request->get('Authorization');

        if (!$token) {
            throw new \Exception('Token does not exist', Response::HTTP_UNAUTHORIZED);
        }

        $this->token = $token;
        $dataToken = (array)AuthJwt::GetDataSimple($token);
        return $dataToken;
    }

    /** VALIDA A SOLO LOS DE ROL `Admin` O LOS IDS QUE SE CONSIDEREN COMO ADMINISTRADORES PARA LA API */
    public function AuthorizationAdmins(Request $request = null): void
    {
        $auth = $this->Authorization($request);

        $nelsonId = 234;
        $paulaId = 43;

        $usersIdsAuthorized = [ $nelsonId, $paulaId ];

        if ($auth->isAdminAuthorized) {
            return;
        }

        if (!in_array($auth->idUser, $usersIdsAuthorized)) {
            throw new \Exception('No se le permite esta acción', Response::HTTP_FORBIDDEN);
        }
    }

    /** VALIDA A SOLO USUARIO CON EL `tblusuariose.id` SEA EL MISMO `$idWithPermission` */
    public function AuthorizationUserUniq(int $idWithPermission, Request $request = null): void
    {
        $auth = $this->Authorization($request);
        if ($auth->idUser != $idWithPermission) {
            throw new \Exception('No se le permite esta acción', Response::HTTP_FORBIDDEN);
        }
    }


    /**
     * Optiene un TIMEOUT desde header o bodyRequest y recibe un time opcional
     *
     * @param int $defaultTime - Tiempo de la normal de espera
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function TimeOut(int $defaultTime = 20): int
    {
        $request = request();
        if ($request->get('timeOut')) {
            return intval($request->get('timeOut'));
        }

        $dsTime = ['timeOut', 'TimeOut', 'timeout', 'TIMEOUT'];
        $header = [...$request->request];
        foreach ($dsTime as $t) {
            if ($request->header($t)) {
                return intval($request->header($t));
            }
            if (isset($header[$t])) {
                return intval($header[$t]);
            }
        }

        return $defaultTime;
    }

}
