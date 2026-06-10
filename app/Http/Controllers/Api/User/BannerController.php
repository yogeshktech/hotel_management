<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\User\BannerResource;
use App\Models\Banner;
use OpenApi\Attributes as OA;

class BannerController extends ApiController
{
    #[OA\Get(path: '/user/banners', tags: ['Banners'], summary: 'Active homepage banners')]
    #[OA\Parameter(name: 'placement', in: 'query', schema: new OA\Schema(type: 'string', example: 'home'))]
    #[OA\Response(response: 200, description: 'List of banners')]
    public function index()
    {
        $banners = Banner::where('active', true)
            ->when(request('placement'), fn ($q, $p) => $q->where('placement', $p))
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->orderBy('order')
            ->get();

        return $this->success(BannerResource::collection($banners));
    }
}
