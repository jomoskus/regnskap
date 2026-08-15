<?php

use App\Enums\Category;
use App\Models\BudgetLine;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Livewire;

test('overview uses categorized spend against budget envelopes', function () {
    $this->travelTo('2026-08-15');

    $user = User::factory()->create();
    $other = User::factory()->create();

    BudgetLine::factory()->for($user)->create([
        'name' => 'Dagligvarer',
        'monthly' => '400.00',
        'daily' => null,
        'weekly' => null,
        'other_monthly' => null,
        'yearly' => null,
    ]);
    BudgetLine::factory()->for($user)->create([
        'name' => 'Strøm',
        'monthly' => '200.00',
        'daily' => null,
        'weekly' => null,
        'other_monthly' => null,
        'yearly' => null,
    ]);

    Transaction::factory()->for($user)->create([
        'payee' => 'Test Rema',
        'amount' => '150.00',
        'category' => Category::Dagligvarer,
        'booked_on' => '2026-08-10',
    ]);
    Transaction::factory()->for($user)->create([
        'payee' => 'Ukategorisert kiosk',
        'amount' => '80.00',
        'category' => null,
        'booked_on' => '2026-08-11',
    ]);
    Transaction::factory()->for($user)->create([
        'payee' => 'Forrige måned',
        'amount' => '90.00',
        'category' => Category::Dagligvarer,
        'booked_on' => '2026-07-10',
    ]);
    Transaction::factory()->for($other)->create([
        'payee' => 'Andre sin butikk',
        'amount' => '999.00',
        'category' => Category::Dagligvarer,
        'booked_on' => '2026-08-10',
    ]);

    $this->actingAs($user);

    Livewire::test('pages::overview')
        ->assertSee('Dagligvarer')
        ->assertSee('Strøm')
        ->assertSee('150')
        ->assertSee('400')
        ->assertDontSee('Ukategorisert kiosk')
        ->assertDontSee('Andre sin butikk')
        ->assertDontSee('999');
});

test('guests are redirected from overview', function () {
    $this->get(route('overview'))
        ->assertRedirect(route('login'));
});
