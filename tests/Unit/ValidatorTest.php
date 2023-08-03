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
            'password'=> 'secret_pass',
            'name'    => 'pepito perez',
        ]);
        $validated = Validations::UserRegister($request);
        $this->assertTrue(count($validated->errors()) == 0);

    }

    public function test_validador_de_login(): void
    {
        $request = new Request([
            'user'    => 'user2',
            'password'=> 'super_secret_pass',
        ]);
        $validated = Validations::UserLogin($request);
        $this->assertTrue(count($validated->errors()) == 0);
    }
}
