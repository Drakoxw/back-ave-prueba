<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Tools\AuthJwt;

class AuthJwtTest extends TestCase
{

    public function test_creacion_del_token(): void
    {
        $data = [
            'user'=> 'Drako_user',
            'ds'  => 'pepito',
            'id'  => 666,
            'rol' => 'super rol'
        ];
        $token = AuthJwt::SignInTokenSimple($data);
        $this->assertIsString( $token);
    }

    public function test_validacion_del_token(): void
    {
        $data = [
            'user'=> 'Drako_user_otro',
            'ds'  => 'pepito perez',
            'id'  => 999,
            'rol' => 'super trol'
        ];
        $token = AuthJwt::SignInTokenSimple($data);
        $dataToken = (array)AuthJwt::GetDataSimple($token);
        $this->assertEquals($data['user'], $dataToken['user']);
    }
}
