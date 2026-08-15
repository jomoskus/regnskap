<?php

namespace App\Http\Controllers\Api;

use App\Actions\ImportTransactions;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransactionImportController extends Controller
{
    public function __invoke(Request $request, ImportTransactions $importer): JsonResponse
    {
        $this->authorize('create', Transaction::class);

        $path = $this->storeImport($request);

        return response()->json($importer($this->user($request), $path), 201);
    }

    private function storeImport(Request $request): string
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            ]);

            $file = $request->file('file');

            if (! $file instanceof UploadedFile) {
                throw ValidationException::withMessages([
                    'file' => __('Last opp en CSV-fil eller send CSV i forespørselen.'),
                ]);
            }

            $path = $file->store('imports', 'local');

            if (! is_string($path)) {
                throw ValidationException::withMessages([
                    'file' => __('Kunne ikke lagre filen.'),
                ]);
            }

            return $path;
        }

        $contents = $request->getContent();

        if (trim($contents) === '') {
            throw ValidationException::withMessages([
                'file' => __('Last opp en CSV-fil eller send CSV i forespørselen.'),
            ]);
        }

        $path = 'imports/'.Str::uuid().'.csv';
        Storage::disk('local')->put($path, $contents);

        return $path;
    }
}
