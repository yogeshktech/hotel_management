<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\User\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class ProfileController extends ApiController
{
    #[OA\Get(path: '/user/profile', tags: ['Profile'], summary: 'Get profile', security: [['sanctum' => []]])]
    #[OA\Response(response: 200, description: 'Profile data')]
    public function show(Request $request)
    {
        return $this->success(new CustomerResource($request->user()));
    }

    #[OA\Put(path: '/user/profile', tags: ['Profile'], summary: 'Update profile', security: [['sanctum' => []]])]
    #[OA\RequestBody(content: new OA\JsonContent(properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'phone', type: 'string'),
        new OA\Property(property: 'address', type: 'string'),
        new OA\Property(property: 'city', type: 'string'),
        new OA\Property(property: 'password', type: 'string'),
        new OA\Property(property: 'password_confirmation', type: 'string'),
    ]))]
    #[OA\Response(response: 200, description: 'Profile updated')]
    public function update(Request $request)
    {
        $customer = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customers,email,' . $customer->id,
            'phone' => 'sometimes|required|string|max:20|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $customer->update(collect($validated)->except(['password', 'password_confirmation'])->toArray());

        if ($request->filled('password')) {
            $customer->update(['password' => Hash::make($validated['password'])]);
        }

        return $this->success(new CustomerResource($customer->fresh()), 'Profile updated');
    }
}
