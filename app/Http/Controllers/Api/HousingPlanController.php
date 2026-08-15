<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HousingPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HousingPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', HousingPlan::class);

        return response()->json(
            $this->user($request)->housingPlans()->orderBy('horizon_year')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', HousingPlan::class);

        $plan = $this->user($request)->housingPlans()->create($this->payload($request));

        return response()->json($plan, 201);
    }

    public function update(Request $request, HousingPlan $housingPlan): JsonResponse
    {
        $this->authorize('update', $housingPlan);

        $housingPlan->update($this->payload($request, updating: true));

        return response()->json($housingPlan->refresh());
    }

    public function destroy(HousingPlan $housingPlan): JsonResponse
    {
        $this->authorize('delete', $housingPlan);

        $housingPlan->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        $money = [
            'sale_price',
            'mortgage_on_sold',
            'equity_from_sale',
            'saving_per_year',
            'saved_total',
            'expected_income',
            'possible_loan',
            'student_loan',
            'mortgage',
            'purchase_price',
        ];

        $rules = [
            'horizon_year' => [$required, 'integer', 'min:2000', 'max:2100'],
        ];

        foreach ($money as $field) {
            $rules[$field] = ['sometimes', 'nullable', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);

        if (array_key_exists('horizon_year', $validated)) {
            $validated['horizon_year'] = (int) $validated['horizon_year'];
        }

        foreach ($money as $field) {
            if (! array_key_exists($field, $validated)) {
                continue;
            }

            $validated[$field] = $validated[$field] === null || $validated[$field] === ''
                ? null
                : number_format((float) $validated[$field], 2, '.', '');
        }

        return $validated;
    }
}
