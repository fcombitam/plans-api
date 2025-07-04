<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterTenantRequest;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function register(RegisterTenantRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $data['tenant_code'] = (string) $data['tenant_code'];
        $data['phone_number'] = (string) $data['phone_number'];

        $tenant = Tenant::create($data);

        $tokens = $tenant->tokens()->orderBy('created_at')->get();

        if ($tokens->count() >= 3) {
            $tokens->first()->delete();
        }

        $token = $tenant->createToken('auth-token', ['tenant-logged']);

        return response()->json([
            'message' => 'Registro realizado correctamente',
            'token' => $token->plainTextToken,
            'status' => true,
            'data' => $tenant
        ], 200);
    }
}
