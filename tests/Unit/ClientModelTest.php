<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Tools\Crypter;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Services\Validations;

class ClientModelTest extends TestCase
{

    public function test_creacion_de_cliente(): void
    {
        $dataFactory = Cliente::factory()->make()->toArray();
        $request = new Request([
            'name'     => $dataFactory['name'],
            'lastname' => $dataFactory['lastname'],
            'document' => (string)$dataFactory['document'],
            'email'    => $dataFactory['email'],
            'user'     => $dataFactory['user'],
            'password' => '97876542132655',
        ]);

        $validated = Validations::ClientRegister($request);
        $this->assertTrue(count($validated->errors()) == 0);
        $id = Cliente::CrearCliente($request);
        $this->assertTrue($id > 0);
        Cliente::DeleteRealById($id);
    }

    public function test_actualizacion_de_cliente(): void
    {
        $dataFactory = Cliente::factory()->make()->toArray();
        $request = new Request([
            'name'     => $dataFactory['name'],
            'lastname' => $dataFactory['lastname'],
        ]);

        $validated = Validations::ClientUpdate($request);
        $this->assertTrue(count($validated->errors()) == 0);
        Cliente::ActualizarCliente($request, 2);
    }

    public function test_eliminacion_de_cliente(): void
    {
        Cliente::destroy(2);
        $this->assertTrue(Cliente::find(2) == null);
    }

    public function test_listado_de_clientes(): void
    {
        $lis = Cliente::all();
        $pas = 1234567890;
        print_r(Crypter::encryptAES($pas));
        $this->assertTrue(count($lis) > 0);
    }
}
