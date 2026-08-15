<?php

use App\Models\HousingPlan;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Bolig')] class extends Component
{
    public ?int $editingId = null;

    public string $horizon_year = '';

    public string $sale_price = '';

    public string $mortgage_on_sold = '';

    public string $equity_from_sale = '';

    public string $saving_per_year = '';

    public string $saved_total = '';

    public string $expected_income = '';

    public string $possible_loan = '';

    public string $student_loan = '';

    public string $mortgage = '';

    public string $purchase_price = '';

    public function mount(): void
    {
        $this->authorize('viewAny', HousingPlan::class);

        if ($this->horizon_year === '') {
            $this->horizon_year = (string) now()->year;
        }
    }

    /**
     * @return Collection<int, HousingPlan>
     */
    #[Computed]
    public function plans(): Collection
    {
        return Auth::user()->housingPlans()->orderBy('horizon_year')->get();
    }

    public function edit(int $id): void
    {
        $plan = HousingPlan::query()->findOrFail($id);
        $this->authorize('update', $plan);

        $this->editingId = $plan->id;
        $this->horizon_year = (string) $plan->horizon_year;
        $this->sale_price = $plan->sale_price !== null ? (string) $plan->sale_price : '';
        $this->mortgage_on_sold = $plan->mortgage_on_sold !== null ? (string) $plan->mortgage_on_sold : '';
        $this->equity_from_sale = $plan->equity_from_sale !== null ? (string) $plan->equity_from_sale : '';
        $this->saving_per_year = $plan->saving_per_year !== null ? (string) $plan->saving_per_year : '';
        $this->saved_total = $plan->saved_total !== null ? (string) $plan->saved_total : '';
        $this->expected_income = $plan->expected_income !== null ? (string) $plan->expected_income : '';
        $this->possible_loan = $plan->possible_loan !== null ? (string) $plan->possible_loan : '';
        $this->student_loan = $plan->student_loan !== null ? (string) $plan->student_loan : '';
        $this->mortgage = $plan->mortgage !== null ? (string) $plan->mortgage : '';
        $this->purchase_price = $plan->purchase_price !== null ? (string) $plan->purchase_price : '';
    }

    public function cancel(): void
    {
        $this->editingId = null;
        $this->horizon_year = (string) now()->year;
        $this->reset(
            'sale_price',
            'mortgage_on_sold',
            'equity_from_sale',
            'saving_per_year',
            'saved_total',
            'expected_income',
            'possible_loan',
            'student_loan',
            'mortgage',
            'purchase_price',
        );
    }

    public function save(): void
    {
        $validated = $this->validate([
            'horizon_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'mortgage_on_sold' => ['nullable', 'numeric', 'min:0'],
            'equity_from_sale' => ['nullable', 'numeric', 'min:0'],
            'saving_per_year' => ['nullable', 'numeric', 'min:0'],
            'saved_total' => ['nullable', 'numeric', 'min:0'],
            'expected_income' => ['nullable', 'numeric', 'min:0'],
            'possible_loan' => ['nullable', 'numeric', 'min:0'],
            'student_loan' => ['nullable', 'numeric', 'min:0'],
            'mortgage' => ['nullable', 'numeric', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ([
            'sale_price',
            'mortgage_on_sold',
            'equity_from_sale',
            'saving_per_year',
            'saved_total',
            'expected_income',
            'possible_loan',
            'student_loan',
            'mortgage',
            'purchase_price',
        ] as $field) {
            $validated[$field] = $validated[$field] === null || $validated[$field] === ''
                ? null
                : number_format((float) $validated[$field], 2, '.', '');
        }

        $validated['horizon_year'] = (int) $validated['horizon_year'];

        if ($this->editingId !== null) {
            $plan = HousingPlan::query()->findOrFail($this->editingId);
            $this->authorize('update', $plan);
            $plan->update($validated);
        } else {
            $this->authorize('create', HousingPlan::class);
            Auth::user()->housingPlans()->create($validated);
        }

        Flux::toast(variant: 'success', text: __('Lagret.'));
        $this->cancel();
        unset($this->plans);
    }

    public function delete(int $id): void
    {
        $plan = HousingPlan::query()->findOrFail($id);
        $this->authorize('delete', $plan);
        $plan->delete();

        if ($this->editingId === $id) {
            $this->cancel();
        }

        Flux::toast(variant: 'success', text: __('Slettet.'));
        unset($this->plans);
    }
}; ?>

<div class="mx-auto flex w-full max-w-3xl flex-col gap-6 py-4">
    <flux:heading size="xl">{{ __('Bolig') }}</flux:heading>

    <form wire:submit="save" class="space-y-4 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
        <flux:heading size="sm">{{ $editingId ? __('Rediger plan') : __('Ny plan') }}</flux:heading>
        <flux:input wire:model="horizon_year" type="number" min="2000" max="2100" :label="__('År')" required />
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <flux:input wire:model="sale_price" type="number" step="0.01" min="0" :label="__('Salgspris')" />
            <flux:input wire:model="mortgage_on_sold" type="number" step="0.01" min="0" :label="__('Lån på solgt')" />
            <flux:input wire:model="equity_from_sale" type="number" step="0.01" min="0" :label="__('Egenkapital fra salg')" />
            <flux:input wire:model="saving_per_year" type="number" step="0.01" min="0" :label="__('Sparing per år')" />
            <flux:input wire:model="saved_total" type="number" step="0.01" min="0" :label="__('Spart totalt')" />
            <flux:input wire:model="expected_income" type="number" step="0.01" min="0" :label="__('Forventet inntekt')" />
            <flux:input wire:model="possible_loan" type="number" step="0.01" min="0" :label="__('Mulig lån')" />
            <flux:input wire:model="student_loan" type="number" step="0.01" min="0" :label="__('Studielån')" />
            <flux:input wire:model="mortgage" type="number" step="0.01" min="0" :label="__('Boliglån')" />
            <flux:input wire:model="purchase_price" type="number" step="0.01" min="0" :label="__('Kjøpspris')" />
        </div>
        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">{{ __('Lagre') }}</flux:button>
            @if ($editingId)
                <flux:button type="button" variant="subtle" wire:click="cancel">{{ __('Avbryt') }}</flux:button>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                    <th class="py-2 pe-3">{{ __('År') }}</th>
                    <th class="py-2 pe-3">{{ __('Salgspris') }}</th>
                    <th class="py-2 pe-3">{{ __('Egenkapital') }}</th>
                    <th class="py-2 pe-3">{{ __('Kjøpspris') }}</th>
                    <th class="py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->plans as $plan)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800">
                        <td class="py-3 pe-3 tabular-nums">{{ $plan->horizon_year }}</td>
                        <td class="py-3 pe-3 tabular-nums">{{ $plan->sale_price !== null ? number_format((float) $plan->sale_price, 0, ',', ' ') : '—' }}</td>
                        <td class="py-3 pe-3 tabular-nums">{{ $plan->equity_from_sale !== null ? number_format((float) $plan->equity_from_sale, 0, ',', ' ') : '—' }}</td>
                        <td class="py-3 pe-3 tabular-nums">{{ $plan->purchase_price !== null ? number_format((float) $plan->purchase_price, 0, ',', ' ') : '—' }}</td>
                        <td class="py-3">
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="subtle" wire:click="edit({{ $plan->id }})">{{ __('Rediger') }}</flux:button>
                                <flux:button size="sm" variant="subtle" wire:click="delete({{ $plan->id }})" wire:confirm="{{ __('Slette denne?') }}">{{ __('Slett') }}</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3">{{ __('Ingen boligplaner ennå.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
