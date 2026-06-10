<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\User\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends ApiController
{
    #[OA\Post(path: '/user/auth/register', tags: ['Auth'], summary: 'Register new customer')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'email', 'phone', 'password', 'password_confirmation'], properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Amit Verma'),
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'phone', type: 'string', example: '+919900000001'),
        new OA\Property(property: 'password', type: 'string', format: 'password'),
        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
    ]))]
    #[OA\Response(response: 201, description: 'Registered successfully')]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Customer::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $token = $customer->createToken('user-api')->plainTextToken;

        return $this->success([
            'customer' => new CustomerResource($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Registration successful', 201);
    }

    #[OA\Post(path: '/user/auth/login', tags: ['Auth'], summary: 'Customer login')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['email', 'password'], properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'password', type: 'string', format: 'password'),
        new OA\Property(property: 'device_name', type: 'string', example: 'mobile-app'),
    ]))]
    #[OA\Response(response: 200, description: 'Login successful')]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return $this->error('Invalid credentials', 401);
        }

        if (! $customer->is_active) {
            return $this->error('Account deactivated', 403);
        }

        $customer->update(['last_login_at' => now()]);
        $token = $customer->createToken($request->device_name ?? 'user-api')->plainTextToken;

        return $this->success([
            'customer' => new CustomerResource($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    #[OA\Post(path: '/user/auth/logout', tags: ['Auth'], summary: 'Logout (revoke current token)', security: [['sanctum' => []]])]
    #[OA\Response(response: 200, description: 'Logged out')]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    #[OA\Get(path: '/user/auth/me', tags: ['Auth'], summary: 'Get authenticated customer', security: [['sanctum' => []]])]
    #[OA\Response(response: 200, description: 'Customer profile')]
    public function me(Request $request)
    {
        return $this->success(new CustomerResource($request->user()));
    }
}
