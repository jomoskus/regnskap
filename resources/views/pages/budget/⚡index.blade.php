<?php

use App\Models\BudgetLine;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Budsjett')] class extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public string $daily = '';

    public string $weekly = '';

    public string $monthly = '';

    public string $other_monthly = '';

    public string $yearly = '';

    public string $note = '';

    public function mount(): void
    {
        $this->authorize('viewAny', BudgetLine::class);
    }

    /**
     * @return Collection<int, BudgetLine>
     */
    #[Computed]
    public function lines(): Collection
    {
        return Auth::user()->budgetLines()->orderBy('name')->get();
    }

    public function edit(int $id): void
    {
        $line = BudgetLine::query()->findOrFail($id);
        $this->authorize('update', $line);

        $this->editingId = $line->id;
        $this->name = $line->name;
        $this->daily = $line->daily !== null ? (string) $line->daily : '';
        $this->weekly = $line->weekly !== null ? (string) $line->weekly : '';
        $this->monthly = $line->monthly !== null ? (string) $line->monthly : '';
        $this->other_monthly = $line->other_monthly !== null ? (string) $line->other_monthly : '';
        $this->yearly = $line->yearly !== null ? (string) $line->yearly : '';
        $this->note = (string) ($line->note ?? '');
    }

    public function cancel(): void
    {
        $this->editingId = null;
        $this->reset('name', 'daily', 'weekly', 'monthly', 'other_monthly', 'yearly', 'note');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'daily' => ['nullable', 'numeric', 'min:0'],
            'weekly' => ['nullable', 'numeric', 'min:0'],
            'monthly' => ['nullable', 'numeric', 'min:0'],
            'other_monthly' => ['nullable', 'numeric', 'min:0'],
            'yearly' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        foreach (['daily', 'weekly', 'monthly', 'other_monthly', 'yearly'] as $field) {
            $validated[$field] = $validated[$field] === null || $validated[$field] === ''
                ? null
                : number_format((float) $validated[$field], 2, '.', '');
        }

        if ($this->editingId !== null) {
            $line = BudgetLine::query()->findOrFail($this->editingId);
            $this->authorize('update', $line);
            $line->update($validated);
        } else {
            $this->authorize('create', BudgetLine::class);
            Auth::user()->budgetLines()->create($validated);
        }

        Flux::toast(variant: 'success', text: __('Budsjettposten er lagret.'));
        $this->cancel();
        unset($this->lines);
    }

    public function delete(int $id): void
    {
        $line = BudgetLine::query()->findOrFail($id);
        $this->authorize('delete', $line);
        $line->delete();

        if ($this->editingId === $id) {
            $this->cancel();
        }

        Flux::toast(variant: 'success', text: __('Slettet.'));
        unset($this->lines);
    }
}; ?>

<div class="mx-auto flex w-full max-w-lg flex-col gap-6 py-4">
    <flux:heading size="xl">{{ __('Budsjett') }}</flux:heading>
    <flux:text>{{ __('Poster kan følge en kategori eller være egne konvolutter, som Strøm.') }}</flux:text>

    <form wire:submit="save" class="space-y-4 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
        <flux:heading size="sm">{{ $editingId ? __('Rediger post') : __('Ny post') }}</flux:heading>
        <flux:input wire:model="name" :label="__('Post')" required />
        <div class="grid grid-cols-2 gap-3">
            <flux:input wire:model="daily" type="number" step="0.01" min="0" :label="__('Daglig')" />
            <flux:input wire:model="weekly" type="number" step="0.01" min="0" :label="__('Ukentlig')" />
            <flux:input wire:model="monthly" type="number" step="0.01" min="0" :label="__('Månedlig')" />
            <flux:input wire:model="other_monthly" type="number" step="0.01" min="0" :label="__('Annet månedlig')" />
            <flux:input wire:model="yearly" type="number" step="0.01" min="0" :label="__('Årlig')" class="col-span-2" />
        </div>
        <flux:textarea wire:model="note" :label="__('Notat')" rows="2" />
        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">{{ __('Lagre') }}</flux:button>
            @if ($editingId)
                <flux:button type="button" variant="subtle" wire:click="cancel">{{ __('Avbryt') }}</flux:button>
            @endif
        </div>
    </form>

    <div class="space-y-3">
        @forelse ($this->lines as $line)
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex items-baseline justify-between gap-3">
                    <flux:heading size="sm">{{ $line->name }}</flux:heading>
                    <p class="font-semibold tabular-nums">{{ number_format((float) $line->monthly_nok, 0, ',', ' ') }} kr/mnd</p>
                </div>
                @if ($line->note)
                    <flux:text class="mt-1 text-sm">{{ $line->note }}</flux:text>
                @endif
                <div class="mt-3 flex gap-3">
                    <flux:button size="sm" variant="subtle" wire:click="edit({{ $line->id }})">{{ __('Rediger') }}</flux:button>
                    <flux:button size="sm" variant="subtle" wire:click="delete({{ $line->id }})" wire:confirm="{{ __('Slette denne posten?') }}">{{ __('Slett') }}</flux:button>
                </div>
            </div>
        @empty
            <flux:text>{{ __('Ingen budsjettposter ennå.') }}</flux:text>
        @endforelse
    </div>
</div>
