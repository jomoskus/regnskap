<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', BudgetLine::class);

        return response()->json(
            $this->user($request)->budgetLines()->orderBy('name')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', BudgetLine::class);

        $line = $this->user($request)->budgetLines()->create($this->payload($request));

        return response()->json($line, 201);
    }

    public function update(Request $request, BudgetLine $budgetLine): JsonResponse
    {
        $this->authorize('update', $budgetLine);

        $budgetLine->update($this->payload($request, updating: true));

        return response()->json($budgetLine->refresh());
    }

    public function destroy(BudgetLine $budgetLine): JsonResponse
    {
        $this->authorize('delete', $budgetLine);

        $budgetLine->delete();

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
            'daily' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'weekly' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'monthly' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'other_monthly' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'yearly' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'note' => ['sometimes', 'nullable', 'string'],
        ]);

        foreach (['daily', 'weekly', 'monthly', 'other_monthly', 'yearly'] as $field) {
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
