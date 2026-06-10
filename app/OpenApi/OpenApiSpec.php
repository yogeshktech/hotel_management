<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Hotel Booking — User API',
    description: 'Customer-facing REST API for mobile/web user UI. Authenticate with Bearer token from login/register.'
)]
#[OA\Server(url: '/api/v1', description: 'API v1')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum Token',
    description: 'Use token from POST /user/auth/login or /user/auth/register'
)]
#[OA\Tag(name: 'Auth', description: 'Customer registration & login')]
#[OA\Tag(name: 'Profile', description: 'Customer profile')]
#[OA\Tag(name: 'Dashboard', description: 'Customer dashboard stats')]
#[OA\Tag(name: 'Locations', description: 'Browse destinations')]
#[OA\Tag(name: 'Properties', description: 'Hotels & resorts')]
#[OA\Tag(name: 'Bookings', description: 'Online booking & tracking')]
#[OA\Tag(name: 'Reviews', description: 'Post-stay reviews')]
#[OA\Tag(name: 'Promo', description: 'Promo code validation')]
#[OA\Tag(name: 'Banners', description: 'Homepage banners')]
class OpenApiSpec
{
}
