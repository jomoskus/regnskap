<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('inbox') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="inbox" :href="route('inbox')" :current="request()->routeIs('inbox')" wire:navigate>
                    {{ __('Innboks') }}
                </flux:navbar.item>
                <flux:navbar.item icon="chart-bar" :href="route('overview')" :current="request()->routeIs('overview')" wire:navigate>
                    {{ __('Oversikt') }}
                </flux:navbar.item>
                <flux:navbar.item icon="queue-list" :href="route('transactions.index')" :current="request()->routeIs('transactions.index')" wire:navigate>
                    {{ __('Transaksjoner') }}
                </flux:navbar.item>
                <flux:navbar.item icon="plus" :href="route('transactions.create')" :current="request()->routeIs('transactions.create')" wire:navigate>
                    {{ __('Ny') }}
                </flux:navbar.item>
                <flux:navbar.item icon="arrow-up-tray" :href="route('transactions.import')" :current="request()->routeIs('transactions.import')" wire:navigate>
                    {{ __('Import') }}
                </flux:navbar.item>
                <flux:navbar.item icon="calculator" :href="route('budget.index')" :current="request()->routeIs('budget.index')" wire:navigate>
                    {{ __('Budsjett') }}
                </flux:navbar.item>
                <flux:navbar.item icon="banknotes" :href="route('wealth.index')" :current="request()->routeIs('wealth.index')" wire:navigate>
                    {{ __('Formue') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />


            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('inbox') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Regnskap')">
                    <flux:sidebar.item icon="inbox" :href="route('inbox')" :current="request()->routeIs('inbox')" wire:navigate>
                        {{ __('Innboks') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chart-bar" :href="route('overview')" :current="request()->routeIs('overview')" wire:navigate>
                        {{ __('Oversikt') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="queue-list" :href="route('transactions.index')" :current="request()->routeIs('transactions.index')" wire:navigate>
                        {{ __('Transaksjoner') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="plus" :href="route('transactions.create')" :current="request()->routeIs('transactions.create')" wire:navigate>
                        {{ __('Ny') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-up-tray" :href="route('transactions.import')" :current="request()->routeIs('transactions.import')" wire:navigate>
                        {{ __('Import') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                <flux:sidebar.group :heading="__('Plan')">
                    <flux:sidebar.item icon="calculator" :href="route('budget.index')" :current="request()->routeIs('budget.index')" wire:navigate>
                        {{ __('Budsjett') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-path" :href="route('recurring.index')" :current="request()->routeIs('recurring.index')" wire:navigate>
                        {{ __('Faste kostnader') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                <flux:sidebar.group :heading="__('Formue')">
                    <flux:sidebar.item icon="banknotes" :href="route('wealth.index')" :current="request()->routeIs('wealth.index')" wire:navigate>
                        {{ __('Formue') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chart-pie" :href="route('investments.index')" :current="request()->routeIs('investments.index')" wire:navigate>
                        {{ __('Investeringer') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="home" :href="route('housing.index')" :current="request()->routeIs('housing.index')" wire:navigate>
                        {{ __('Bolig') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-library" :href="route('accounts.index')" :current="request()->routeIs('accounts.index')" wire:navigate>
                        {{ __('Kontoer') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

        </flux:sidebar>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
