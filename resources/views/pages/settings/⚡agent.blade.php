<?php

use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Agent')] class extends Component
{
    public string $name = 'Personlig regnskapsfører';

    public ?string $plainTextToken = null;

    /**
     * @return Collection<int, PersonalAccessToken>
     */
    #[Computed]
    public function tokens(): Collection
    {
        return Auth::user()->tokens()->orderByDesc('created_at')->get();
    }

    public function createToken(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token = Auth::user()->createToken($validated['name']);
        $this->plainTextToken = $token->plainTextToken;
        $this->name = 'Personlig regnskapsfører';

        unset($this->tokens);
    }

    public function revoke(int $tokenId): void
    {
        Auth::user()->tokens()->whereKey($tokenId)->delete();
        $this->plainTextToken = null;

        unset($this->tokens);

        Flux::toast(variant: 'success', text: __('Nøkkelen er tilbakekalt.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Agent') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Agent')" :subheading="__('Opprett en API-nøkkel til den personlige regnskapsføreren. Nøkkelen vises bare én gang.')">
        <form wire:submit="createToken" class="my-6 w-full space-y-6">
            <flux:input
                wire:model="name"
                :label="__('Navn på nøkkel')"
                type="text"
                required
                autocomplete="off"
            />

            <flux:button variant="primary" type="submit" data-test="create-agent-token">
                {{ __('Opprett nøkkel') }}
            </flux:button>
        </form>

        @if ($plainTextToken)
            <div class="mb-6 space-y-2 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700" data-test="new-agent-token">
                <flux:heading size="sm">{{ __('Kopier nøkkelen nå') }}</flux:heading>
                <flux:text>{{ __('Dette er eneste gang den vises i klartekst. Lim den inn i agenten som en hemmelighet — aldri i git.') }}</flux:text>
                <flux:input
                    :value="$plainTextToken"
                    type="text"
                    readonly
                    autocomplete="off"
                    class="font-mono"
                />
            </div>
        @endif

        <div class="space-y-3">
            <flux:heading size="sm">{{ __('Eksisterende nøkler') }}</flux:heading>

            @forelse ($this->tokens as $token)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <div>
                        <flux:heading size="sm">{{ $token->name }}</flux:heading>
                        <flux:text class="text-sm">
                            @if ($token->last_used_at)
                                {{ __('Sist brukt') }}: {{ $token->last_used_at->timezone('Europe/Oslo')->format('d.m.Y H:i') }}
                            @else
                                {{ __('Aldri brukt') }}
                            @endif
                        </flux:text>
                    </div>
                    <flux:button
                        size="sm"
                        variant="subtle"
                        wire:click="revoke({{ $token->id }})"
                        wire:confirm="{{ __('Tilbakekalle denne nøkkelen?') }}"
                    >
                        {{ __('Tilbakekall') }}
                    </flux:button>
                </div>
            @empty
                <flux:text>{{ __('Ingen nøkler ennå.') }}</flux:text>
            @endforelse
        </div>
    </x-pages::settings.layout>
</section>
