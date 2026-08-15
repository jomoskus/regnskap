<?php

use App\Enums\HoldingType;
use App\Models\Holding;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Investeringer')] class extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public string $value = '';

    public string $price = '';

    public string $quantity = '';

    public string $type = 'portefolje';

    public function mount(): void
    {
        $this->authorize('viewAny', Holding::class);
    }

    /**
     * @return Collection<int, Holding>
     */
    #[Computed]
    public function holdings(): Collection
    {
        return Auth::user()->holdings()->orderBy('name')->get();
    }

    #[Computed]
    public function totalValue(): float
    {
        return (float) $this->holdings->sum('value');
    }

    public function edit(int $id): void
    {
        $holding = Holding::query()->findOrFail($id);
        $this->authorize('update', $holding);

        $this->editingId = $holding->id;
        $this->name = $holding->name;
        $this->value = (string) $holding->value;
        $this->price = $holding->price !== null ? (string) $holding->price : '';
        $this->quantity = $holding->quantity !== null ? (string) $holding->quantity : '';
        $this->type = $holding->type->value;
    }

    public function cancel(): void
    {
        $this->editingId = null;
        $this->type = HoldingType::Portefolje->value;
        $this->reset('name', 'value', 'price', 'quantity');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'type' => ['required', Rule::enum(HoldingType::class)],
        ]);

        $payload = [
            'name' => $validated['name'],
            'value' => number_format((float) $validated['value'], 2, '.', ''),
            'price' => $validated['price'] === null || $validated['price'] === ''
                ? null
                : number_format((float) $validated['price'], 4, '.', ''),
            'quantity' => $validated['quantity'] === null || $validated['quantity'] === ''
                ? null
                : number_format((float) $validated['quantity'], 6, '.', ''),
            'type' => HoldingType::from($validated['type']),
        ];

        if ($this->editingId !== null) {
            $holding = Holding::query()->findOrFail($this->editingId);
            $this->authorize('update', $holding);
            $holding->update($payload);
        } else {
            $this->authorize('create', Holding::class);
            Auth::user()->holdings()->create($payload);
        }

        Flux::toast(variant: 'success', text: __('Lagret.'));
        $this->cancel();
        unset($this->holdings, $this->totalValue);
    }

    public function delete(int $id): void
    {
        $holding = Holding::query()->findOrFail($id);
        $this->authorize('delete', $holding);
        $holding->delete();

        if ($this->editingId === $id) {
            $this->cancel();
        }

        Flux::toast(variant: 'success', text: __('Slettet.'));
        unset($this->holdings, $this->totalValue);
    }
}; ?>

<div class="mx-auto flex w-full max-w-lg flex-col gap-6 py-4">
    <div class="flex items-baseline justify-between gap-4">
        <flux:heading size="xl">{{ __('Investeringer') }}</flux:heading>
        <p class="font-semibold tabular-nums">{{ number_format($this->totalValue, 0, ',', ' ') }} kr</p>
    </div>

    <form wire:submit="save" class="space-y-4 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
        <flux:heading size="sm">{{ $editingId ? __('Rediger') : __('Ny beholdning') }}</flux:heading>
        <flux:input wire:model="name" :label="__('Navn')" required />
        <flux:select wire:model="type" :label="__('Type')" required>
            @foreach (\App\Enums\HoldingType::cases() as $type)
                <option value="{{ $type->value }}">{{ $type->label() }}</option>
            @endforeach
        </flux:select>
        <flux:input wire:model="value" type="number" step="0.01" min="0" :label="__('Verdi')" required />
        <div class="grid grid-cols-2 gap-3">
            <flux:input wire:model="price" type="number" step="0.0001" min="0" :label="__('Kurs')" />
            <flux:input wire:model="quantity" type="number" step="0.000001" min="0" :label="__('Antall')" />
        </div>
        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">{{ __('Lagre') }}</flux:button>
            @if ($editingId)
                <flux:button type="button" variant="subtle" wire:click="cancel">{{ __('Avbryt') }}</flux:button>
            @endif
        </div>
    </form>

    <div class="space-y-3">
        @forelse ($this->holdings as $holding)
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex items-baseline justify-between gap-3">
                    <div>
                        <flux:heading size="sm">{{ $holding->name }}</flux:heading>
                        <flux:text class="text-sm">{{ $holding->type->label() }}</flux:text>
                    </div>
                    <p class="font-semibold tabular-nums">{{ number_format((float) $holding->value, 0, ',', ' ') }} kr</p>
                </div>
                <div class="mt-3 flex gap-3">
                    <flux:button size="sm" variant="subtle" wire:click="edit({{ $holding->id }})">{{ __('Rediger') }}</flux:button>
                    <flux:button size="sm" variant="subtle" wire:click="delete({{ $holding->id }})" wire:confirm="{{ __('Slette denne?') }}">{{ __('Slett') }}</flux:button>
                </div>
            </div>
        @empty
            <flux:text>{{ __('Ingen beholdninger ennå.') }}</flux:text>
        @endforelse
    </div>
</div>
