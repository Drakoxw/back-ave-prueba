<?php

namespace App\Models;

use Exception;
use App\Tools\Crypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

define('Cliente', 'cliente');
class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = Cliente;

    protected $fillable = [
        'name',
        'lastname',
        'document',
        'email',
        'user',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    /** CREACION DE UN CLIENTE */
    public static function CrearCliente(Request $request): int
    {
        try {
            $dataSave = [
                'name' => $request->name,
                'lastname' => $request->lastname,
                'document' => $request->document,
                'email' => $request->email,
                'user' => $request->user,
                'password' => Crypter::encryptAES($request->password)
            ];
            return DB::table(Cliente)->insertGetId($dataSave);
        } catch (\Throwable $e) {
            // dd($e);
            throw new Exception("Intenta con otro usuario", 409);
        }
    }

    /** ACTUALIZACION DE UN CLIENTE */
    public static function ActualizarCliente(Request $request, int $clienteId): void
    {
        try {
            $dataSave = [
                'name' => $request->name,
                'lastname' => $request->lastname,
            ];
            DB::table(Cliente)->where('id', $clienteId)->update($dataSave);
        } catch (\Throwable $e) {
            throw new Exception("No se pudo actualizar el cliente", 409);
        }
    }

    /**SOLO PARA LOS TEST */
    public static function DeleteRealById($clienteId): void
    {
        DB::table(Cliente)->where('id', $clienteId)->delete();
    }
}
