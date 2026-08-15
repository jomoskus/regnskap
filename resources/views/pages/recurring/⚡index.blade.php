<?php

use App\Enums\RecurringInterval;
use App\Models\RecurringCost;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Faste kostnader')] class extends Component
{
    public ?int $editingId = null;

    public string $name = '';

    public string $amount = '';

    public string $currency = 'NOK';

    public string $monthly_nok = '';

    public string $renews_on = '';

    public string $interval = 'monthly';

    public string $payment_method = '';

    public string $note = '';

    public function mount(): void
    {
        $this->authorize('viewAny', RecurringCost::class);
    }

    /**
     * @return Collection<int, RecurringCost>
     */
    #[Computed]
    public function costs(): Collection
    {
        return Auth::user()->recurringCosts()->orderBy('name')->get();
    }

    #[Computed]
    public function monthlyTotal(): float
    {
        return (float) $this->costs->sum('monthly_nok');
    }

    public function edit(int $id): void
    {
        $cost = RecurringCost::query()->findOrFail($id);
        $this->authorize('update', $cost);

        $this->editingId = $cost->id;
        $this->name = $cost->name;
        $this->amount = (string) $cost->amount;
        $this->currency = $cost->currency;
        $this->monthly_nok = (string) $cost->monthly_nok;
        $this->renews_on = $cost->renews_on?->toDateString() ?? '';
        $this->interval = $cost->interval->value;
        $this->payment_method = (string) ($cost->payment_method ?? '');
        $this->note = (string) ($cost->note ?? '');
    }

    public function cancel(): void
    {
        $this->editingId = null;
        $this->interval = RecurringInterval::Monthly->value;
        $this->currency = 'NOK';
        $this->reset('name', 'amount', 'monthly_nok', 'renews_on', 'payment_method', 'note');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:8'],
            'monthly_nok' => ['nullable', 'numeric', 'min:0'],
            'renews_on' => ['nullable', 'date'],
            'interval' => ['required', Rule::enum(RecurringInterval::class)],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $interval = RecurringInterval::from($validated['interval']);
        $amount = number_format((float) $validated['amount'], 2, '.', '');
        $monthly = $validated['monthly_nok'] !== null && $validated['monthly_nok'] !== ''
            ? number_format((float) $validated['monthly_nok'], 2, '.', '')
            : RecurringCost::monthlyEquivalent($amount, $interval);

        $payload = [
            'name' => $validated['name'],
            'amount' => $amount,
            'currency' => $validated['currency'],
            'monthly_nok' => $monthly,
            'renews_on' => $validated['renews_on'] ?: null,
            'interval' => $interval,
            'payment_method' => $validated['payment_method'] ?: null,
            'note' => $validated['note'] ?: null,
        ];

        if ($this->editingId !== null) {
            $cost = RecurringCost::query()->findOrFail($this->editingId);
            $this->authorize('update', $cost);
            $cost->update($payload);
        } else {
            $this->authorize('create', RecurringCost::class);
            Auth::user()->recurringCosts()->create($payload);
        }

        Flux::toast(variant: 'success', text: __('Lagret.'));
        $this->cancel();
        unset($this->costs, $this->monthlyTotal);
    }

    public function delete(int $id): void
    {
        $cost = RecurringCost::query()->findOrFail($id);
        $this->authorize('delete', $cost);
        $cost->delete();

        if ($this->editingId === $id) {
            $this->cancel();
        }

        Flux::toast(variant: 'success', text: __('Slettet.'));
        unset($this->costs, $this->monthlyTotal);
    }
}; ?>

<div class="mx-auto flex w-full max-w-lg flex-col gap-6 py-4">
    <div class="flex items-baseline justify-between gap-4">
        <flux:heading size="xl">{{ __('Faste kostnader') }}</flux:heading>
        <p class="font-semibold tabular-nums">{{ number_format($this->monthlyTotal, 0, ',', ' ') }} kr/mnd</p>
    </div>

    <form wire:submit="save" class="space-y-4 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
        <flux:heading size="sm">{{ $editingId ? __('Rediger') : __('Ny kostnad') }}</flux:heading>
        <flux:input wire:model="name" :label="__('Navn')" required />
        <div class="grid grid-cols-2 gap-3">
            <flux:input wire:model="amount" type="number" step="0.01" min="0" :label="__('Beløp')" required />
            <flux:input wire:model="currency" :label="__('Valuta')" />
        </div>
        <flux:select wire:model="interval" :label="__('Intervall')" required>
            @foreach (\App\Enums\RecurringInterval::cases() as $interval)
                <option value="{{ $interval->value }}">{{ $interval->label() }}</option>
            @endforeach
        </flux:select>
        <flux:input wire:model="monthly_nok" type="number" step="0.01" min="0" :label="__('Månedsbeløp NOK')" :description="__('Tomt felt regnes ut fra beløp og intervall.')" />
        <flux:input wire:model="renews_on" type="date" :label="__('Fornyes')" />
        <flux:input wire:model="payment_method" :label="__('Betalingsmåte')" />
        <flux:textarea wire:model="note" :label="__('Notat')" rows="2" />
        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">{{ __('Lagre') }}</flux:button>
            @if ($editingId)
                <flux:button type="button" variant="subtle" wire:click="cancel">{{ __('Avbryt') }}</flux:button>
            @endif
        </div>
    </form>

    <div class="space-y-3">
        @forelse ($this->costs as $cost)
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <div class="flex items-baseline justify-between gap-3">
                    <flux:heading size="sm">{{ $cost->name }}</flux:heading>
                    <p class="font-semibold tabular-nums">{{ number_format((float) $cost->monthly_nok, 0, ',', ' ') }} kr/mnd</p>
                </div>
                <flux:text class="mt-1 text-sm">
                    {{ number_format((float) $cost->amount, 2, ',', ' ') }} {{ $cost->currency }}
                    · {{ $cost->interval->label() }}
                    @if ($cost->renews_on)
                        · {{ $cost->renews_on->format('d.m.Y') }}
                    @endif
                </flux:text>
                <div class="mt-3 flex gap-3">
                    <flux:button size="sm" variant="subtle" wire:click="edit({{ $cost->id }})">{{ __('Rediger') }}</flux:button>
                    <flux:button size="sm" variant="subtle" wire:click="delete({{ $cost->id }})" wire:confirm="{{ __('Slette denne?') }}">{{ __('Slett') }}</flux:button>
                </div>
            </div>
        @empty
            <flux:text>{{ __('Ingen faste kostnader ennå.') }}</flux:text>
        @endforelse
    </div>
</div>
