<?php

namespace App\Http\Controllers\Api;

use App\Enums\FigureSection;
use App\Http\Controllers\Controller;
use App\Models\MonthlyFigure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MonthlyFigureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MonthlyFigure::class);

        $validated = $request->validate([
            'month' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        $query = $this->user($request)->monthlyFigures()->orderBy('month')->orderBy('section')->orderBy('item');

        if (! empty($validated['month'])) {
            $query->whereDate('month', $validated['month'].'-01');
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', MonthlyFigure::class);

        $figure = $this->user($request)->monthlyFigures()->create($this->payload($request));

        return response()->json($figure, 201);
    }

    public function update(Request $request, MonthlyFigure $monthlyFigure): JsonResponse
    {
        $this->authorize('update', $monthlyFigure);

        $monthlyFigure->update($this->payload($request, updating: true));

        return response()->json($monthlyFigure->refresh());
    }

    public function destroy(MonthlyFigure $monthlyFigure): JsonResponse
    {
        $this->authorize('delete', $monthlyFigure);

        $monthlyFigure->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        $month = $request->input('month');
        if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            $request->merge(['month' => $month.'-01']);
        }

        $validated = $request->validate([
            'month' => [$required, 'date'],
            'section' => [$required, Rule::enum(FigureSection::class)],
            'item' => [$required, 'string', 'max:255'],
            'amount' => [$required, 'numeric'],
            'note' => ['sometimes', 'nullable', 'string'],
        ]);

        if (array_key_exists('month', $validated)) {
            $validated['month'] = Carbon::parse($validated['month'])->startOfMonth()->toDateString();
        }

        if (array_key_exists('section', $validated)) {
            $validated['section'] = FigureSection::from($validated['section']);
        }

        if (array_key_exists('amount', $validated)) {
            $validated['amount'] = number_format((float) $validated['amount'], 2, '.', '');
        }

        if (array_key_exists('note', $validated)) {
            $validated['note'] = $validated['note'] ?: null;
        }

        return $validated;
    }
}
