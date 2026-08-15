<?php

namespace App\Http\Controllers\Api;

use App\Enums\RecurringInterval;
use App\Http\Controllers\Controller;
use App\Models\RecurringCost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecurringCostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', RecurringCost::class);

        return response()->json(
            $this->user($request)->recurringCosts()->orderBy('name')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', RecurringCost::class);

        $cost = $this->user($request)->recurringCosts()->create($this->payload($request));

        return response()->json($cost, 201);
    }

    public function update(Request $request, RecurringCost $recurringCost): JsonResponse
    {
        $this->authorize('update', $recurringCost);

        $recurringCost->update($this->payload($request, updating: true, existing: $recurringCost));

        return response()->json($recurringCost->refresh());
    }

    public function destroy(RecurringCost $recurringCost): JsonResponse
    {
        $this->authorize('delete', $recurringCost);

        $recurringCost->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, bool $updating = false, ?RecurringCost $existing = null): array
    {
        $required = $updating ? 'sometimes' : 'required';

        $validated = $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'amount' => [$required, 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'max:8'],
            'monthly_nok' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'renews_on' => ['sometimes', 'nullable', 'date'],
            'interval' => [$updating ? 'sometimes' : 'required', Rule::enum(RecurringInterval::class)],
            'payment_method' => ['sometimes', 'nullable', 'string', 'max:255'],
            'note' => ['sometimes', 'nullable', 'string'],
        ]);

        if (! $updating) {
            $validated['currency'] = $validated['currency'] ?? 'NOK';
        }

        if (array_key_exists('amount', $validated)) {
            $validated['amount'] = number_format((float) $validated['amount'], 2, '.', '');
        }

        if (array_key_exists('interval', $validated)) {
            $validated['interval'] = RecurringInterval::from($validated['interval']);
        }

        if (array_key_exists('renews_on', $validated)) {
            $validated['renews_on'] = $validated['renews_on'] ?: null;
        }

        if (array_key_exists('payment_method', $validated)) {
            $validated['payment_method'] = $validated['payment_method'] ?: null;
        }

        if (array_key_exists('note', $validated)) {
            $validated['note'] = $validated['note'] ?: null;
        }

        $amount = $validated['amount'] ?? $existing?->amount;
        $interval = $validated['interval'] ?? $existing?->interval;

        if (array_key_exists('monthly_nok', $validated) && $validated['monthly_nok'] !== null && $validated['monthly_nok'] !== '') {
            $validated['monthly_nok'] = number_format((float) $validated['monthly_nok'], 2, '.', '');
        } elseif (is_string($amount) && $interval instanceof RecurringInterval) {
            $validated['monthly_nok'] = RecurringCost::monthlyEquivalent($amount, $interval);
        }

        return $validated;
    }
}
