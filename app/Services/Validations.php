<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class Validations
{

  private static function makeValidator(Request $request, $rulesValidator): \Illuminate\Validation\Validator
  {
    $validator = Validator::make($request->all(), $rulesValidator);

    if (count($validator->errors()) > 0) {
      throw new Exception(
        'SerializedError::' . serialize($validator->errors()->messages()),
        Response::HTTP_UNPROCESSABLE_ENTITY
      );
    }

    return $validator;
  }

  public static function UserLogin(Request $request): \Illuminate\Validation\Validator
  {
    return self::makeValidator($request, [
        'user'    => ['required', 'string', 'max:255', 'min:4'],
        'password'=> ['required', 'string', 'max:40', 'min:8'],
    ]);
  }

  public static function UserRegister(Request $request): \Illuminate\Validation\Validator
  {
    return self::makeValidator($request, [
      'user'    => ['required', 'string', 'max:255', 'min:4'],
      'password'=> ['required', 'string', 'max:40', 'min:8'],
      'name'    => ['required', 'string', 'max:255', 'min:4'],
    ]);
  }

  public static function ClientRegister(Request $request): \Illuminate\Validation\Validator
  {
    return self::makeValidator($request, [
        'name'     => ['required', 'string', 'max:255', 'min:4'],
        'lastname' => ['required', 'string', 'max:255', 'min:4'],
        'document' => ['required', 'string', 'max:255', 'min:4'],
        'email'    => ['required', 'email'],
        'user'     => ['required', 'string', 'max:255', 'min:4'],
        'password' => ['required', 'string', 'max:80', 'min:8'],
    ]);
  }

  public static function ClientUpdate(Request $request): \Illuminate\Validation\Validator
  {
    return self::makeValidator($request, [
        'name'     => ['required', 'string', 'max:255', 'min:4'],
        'lastname' => ['required', 'string', 'max:255', 'min:4'],
    ]);
  }

}
