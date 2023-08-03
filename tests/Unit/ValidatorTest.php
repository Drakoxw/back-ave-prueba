<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Services\Validations;

class ValidatorTest extends TestCase
{

    public function test_validador_de_registro(): void
    {
        $request = new Request([
            'user'    => 'user1',
            'password' => 'secret_pass',
            'name'    => 'pepito perez',
        ]);
        $validated = Validations::UserRegister($request);
        $this->assertTrue(count($validated->errors()) == 0);
    }

    public function test_validador_de_login(): void
    {
        $request = new Request([
            'user'    => 'user2',
            'password' => 'super_secret_pass',
        ]);
        $validated = Validations::UserLogin($request);
        $this->assertTrue(count($validated->errors()) == 0);
    }

    public function test_creacion_de_nuevo_cliente(): void
    {
        $request = new Request([
            'name'     => 'Cliente 1',
            'lastname' => 'Apellido 1',
            'document' => '1012100000',
            'email'    => 'dragon12xw@gmail.com',
            'user'     => 'user cli',
            'password' => '12345678',
        ]);
        $validated = Validations::ClientRegister($request);
        $this->assertTrue(count($validated->errors()) == 0);
    }
}
