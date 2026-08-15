<?php

namespace App\Http\Controllers\Api;

use App\Actions\BuildOverview;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function __invoke(Request $request, BuildOverview $overview): JsonResponse
    {
        $this->authorize('viewAny', Transaction::class);

        $validated = $request->validate([
            'month' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        return response()->json($overview($this->user($request), $validated['month'] ?? null));
    }
}
