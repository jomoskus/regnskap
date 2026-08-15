<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Account::class);

        return response()->json(
            $this->user($request)->accounts()->orderBy('name')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Account::class);

        $account = $this->user($request)->accounts()->create($this->payload($request));

        return response()->json($account, 201);
    }

    public function update(Request $request, Account $account): JsonResponse
    {
        $this->authorize('update', $account);

        $account->update($this->payload($request, updating: true));

        return response()->json($account->refresh());
    }

    public function destroy(Account $account): JsonResponse
    {
        $this->authorize('delete', $account);

        $account->delete();

        return response()->json(status: 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        return $request->validate([
            'name' => [$required, 'string', 'max:255'],
            'type' => [$required, 'string', 'max:255'],
        ]);
    }
}
