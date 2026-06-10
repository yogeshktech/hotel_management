<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Hotel Booking — User API',
    description: <<<'DESC'
Customer-facing REST API for mobile/web user UI. Authenticate with Bearer token from `POST /user/auth/login` or `/user/auth/register`.

## Test / Dummy Credentials (from seeders)

**All accounts use password:** `password123`

### Customer — API login (`POST /user/auth/login`) & web (`/customer/login`)
| Name | Email |
|------|-------|
| Amit Verma | amit@customer.com |
| Sneha Patel | sneha@customer.com |
| Rahul Singh | rahul@customer.com |
| Kavita Nair | kavita@customer.com |

### Staff / Admin — web only (`/staff/login`)
| Role | Email | After login |
|------|-------|-------------|
| Super Admin | superadmin@hotel.com | `/admin/dashboard` |
| Admin Staff | staff@hotel.com | `/admin/dashboard` |

### Vendor — web only (`/staff/login`)
| Name | Email | After login |
|------|-------|-------------|
| Rajesh Kumar | vendor1@hotel.com | `/vendor/dashboard` |
| Priya Sharma | vendor2@hotel.com | `/vendor/dashboard` |
| Anil D'Souza | vendor3@hotel.com | `/vendor/dashboard` |
DESC
)]
#[OA\Server(url: '/api/v1', description: 'API v1')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum Token',
    description: 'Use token from POST /user/auth/login or /user/auth/register'
)]
#[OA\Tag(
    name: 'Auth',
    description: 'Customer registration & login. Test login: amit@customer.com / password123 (see API description for all dummy accounts).'
)]
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
