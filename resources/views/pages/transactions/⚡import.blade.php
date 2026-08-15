<?php

use App\Actions\ImportTransactions;
use App\Models\Transaction;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Import')] class extends Component
{
    use WithFileUploads;

    public $upload = null;

    public function mount(): void
    {
        $this->authorize('create', Transaction::class);
    }

    public function import(ImportTransactions $importer): void
    {
        $this->authorize('create', Transaction::class);

        $this->validate([
            'upload' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        /** @var TemporaryUploadedFile $file */
        $file = $this->upload;
        $path = $file->store('imports', 'local');

        $result = $importer(Auth::user(), $path);

        Flux::toast(
            variant: 'success',
            text: __('Importerte :imported rader. Hoppet over :skipped.', [
                'imported' => $result['imported'],
                'skipped' => $result['skipped'],
            ]),
        );

        $this->redirect(route('inbox'), navigate: true);
    }
}; ?>

<div class="mx-auto flex w-full max-w-lg flex-col gap-6 py-4">
    <div class="space-y-2">
        <flux:heading size="xl">{{ __('Import') }}</flux:heading>
        <flux:text>
            {{ __('Last opp en CSV. Bankfiler uten kategori-kolonne lander i innboksen. Har filen kolonnene dato, beløp, kategori, brukersted (og valgfritt betalingsmåte og notat), beholdes de. Ukjent kategori blir stående tom. Duplikater hoppes over.') }}
        </flux:text>
    </div>

    <form wire:submit="import" class="space-y-6">
        <flux:input wire:model="upload" type="file" accept=".csv,text/csv,text/plain" :label="__('CSV-fil')" required />
        <flux:button type="submit" variant="primary">{{ __('Importer') }}</flux:button>
    </form>
</div>
