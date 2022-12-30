<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

    /**
     * @OA\Info(
     *   title="Highleads API",
     *   version="1.0",
     *   description="API highleads-backend",
     *   @OA\Contact(
     *     email="marcoascabral@gmail.com",
     *     name="Marco Aurélio"
     *   )
     * )
     */
class Controller extends BaseController
{
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}
