<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Services\Validations;
use Illuminate\Http\Response;
use App\Http\Controllers\ControllerExt;

class ClienteController extends ControllerExt
{
    public function listAll()
    {
        try {
            $this->AuthorizationAdmins();

            $clientes = Cliente::all();

            if (count($clientes) == 0) {
                return $this->responseVoid();
            }

            return $this->responseOk($clientes->toArray());

        } catch (\Throwable $th) {
            return $this->badResponse($th);
        }
    }

    public function create(Request $request)
    {
        try {
            $this->AuthorizationAdmins($request);
            Validations::ClientRegister($request);

            $id = Cliente::CrearCliente($request);

            return $this->responseOk(
                ['id' => $id],
                'Cliente creado exitosamente.',
                Response::HTTP_CREATED
            );

        } catch (\Throwable $th) {
            return $this->badResponse($th);
        }
    }

    public function show(int $id)
    {
        try {
            $this->AuthorizationAdmins();
            $cliente = Cliente::find($id);

            if (!$cliente) {
                throw new \Exception('Cliente no encontrado', Response::HTTP_NOT_FOUND);
            }

            return $this->responseOk($cliente->toArray());

        } catch (\Throwable $th) {
            return $this->badResponse($th);
        }

    }

    public function update(Request $request, int $id)
    {
        try {
            $this->AuthorizationAdmins();
            Validations::ClientUpdate($request);
            $cliente = Cliente::find($id);

            if (!$cliente) {
                throw new \Exception('Cliente no encontrado', Response::HTTP_NOT_FOUND);
            }

            Cliente::ActualizarCliente($request, $id);

            return $this->responseOk([], 'Cliente actualizado exitosamente');

        } catch (\Throwable $th) {
            return $this->badResponse($th);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->AuthorizationAdmins();

            Cliente::destroy($id);

            return $this->responseVoid();

        } catch (\Throwable $th) {
            return $this->badResponse($th);
        }
    }
}
