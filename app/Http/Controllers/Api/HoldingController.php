<?php

namespace App\Http\Controllers\Api;

use App\Enums\HoldingType;
use App\Http\Controllers\Controller;
use App\Models\Holding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HoldingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Holding::class);

        return response()->json(
            $this->user($request)->holdings()->orderBy('name')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Holding::class);

        $holding = $this->user($request)->holdings()->create($this->payload($request));

        return response()->json($holding, 201);
    }

    public function update(Request $request, Holding $holding): JsonResponse
    {
        $this->authorize('update', $holding);

        $holding->update($this->payload($request, updating: true));

        return response()->json($holding->refresh());
    }

    public function destroy(Holding $holding): JsonResponse
    {
        $this->authorize('delete', $holding);

        $holding->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        $validated = $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'value' => [$required, 'numeric', 'min:0'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'type' => [$updating ? 'sometimes' : 'required', Rule::enum(HoldingType::class)],
        ]);

        if (array_key_exists('value', $validated)) {
            $validated['value'] = number_format((float) $validated['value'], 2, '.', '');
        }

        if (array_key_exists('price', $validated)) {
            $validated['price'] = $validated['price'] === null || $validated['price'] === ''
                ? null
                : number_format((float) $validated['price'], 4, '.', '');
        }

        if (array_key_exists('quantity', $validated)) {
            $validated['quantity'] = $validated['quantity'] === null || $validated['quantity'] === ''
                ? null
                : number_format((float) $validated['quantity'], 6, '.', '');
        }

        if (array_key_exists('type', $validated)) {
            $validated['type'] = HoldingType::from($validated['type']);
        }

        return $validated;
    }
}
