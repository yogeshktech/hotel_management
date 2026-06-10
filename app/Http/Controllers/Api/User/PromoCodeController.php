<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\ApiController;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PromoCodeController extends ApiController
{
    #[OA\Post(path: '/user/promo-codes/validate', tags: ['Promo'], summary: 'Validate promo code')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(required: ['code', 'amount'], properties: [
        new OA\Property(property: 'code', type: 'string', example: 'SUMMER20'),
        new OA\Property(property: 'amount', type: 'number', example: 5000),
    ]))]
    #[OA\Response(response: 200, description: 'Promo validation result')]
    public function validateCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $promo = PromoCode::where('code', $validated['code'])->where('active', true)->first();

        if (! $promo) {
            return $this->error('Invalid promo code', 422);
        }

        if ($promo->valid_from && $promo->valid_from->isFuture()) {
            return $this->error('Promo code not yet active', 422);
        }

        if ($promo->valid_until && $promo->valid_until->isPast()) {
            return $this->error('Promo code expired', 422);
        }

        if ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
            return $this->error('Promo code usage limit reached', 422);
        }

        if ($promo->min_amount && $validated['amount'] < $promo->min_amount) {
            return $this->error('Minimum amount not met', 422);
        }

        $discount = $promo->type === 'percentage'
            ? round($validated['amount'] * ($promo->value / 100), 2)
            : (float) $promo->value;

        return $this->success([
            'code' => $promo->code,
            'type' => $promo->type,
            'value' => $promo->value,
            'discount' => $discount,
            'final_amount' => max(0, $validated['amount'] - $discount),
        ]);
    }
}
