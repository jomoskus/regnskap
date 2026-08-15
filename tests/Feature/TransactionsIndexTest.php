<?php

use App\Enums\Category;
use App\Enums\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Livewire;

test('the transaction list is filterable and hides other users rows', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Transaction::factory()->for($user)->create([
        'payee' => 'Synlig butikk',
        'amount' => '33.00',
        'category' => Category::Dagligvarer,
        'payment_method' => PaymentMethod::Kredittkort,
        'booked_on' => '2026-08-08',
    ]);
    Transaction::factory()->for($user)->create([
        'payee' => 'Annen måned',
        'amount' => '12.00',
        'category' => Category::Annet,
        'booked_on' => '2026-07-08',
    ]);
    Transaction::factory()->for($other)->create([
        'payee' => 'Fremmed butikk',
        'amount' => '44.00',
        'category' => Category::Dagligvarer,
        'booked_on' => '2026-08-08',
    ]);

    $this->actingAs($user);

    Livewire::test('pages::transactions.index')
        ->set('month', '2026-08')
        ->assertSee('Synlig butikk')
        ->assertDontSee('Annen måned')
        ->assertDontSee('Fremmed butikk');

    Livewire::test('pages::transactions.index')
        ->set('month', '2026-08')
        ->set('search', 'Synlig')
        ->assertSee('Synlig butikk');

    Livewire::test('pages::transactions.index')
        ->set('month', '2026-08')
        ->set('category', Category::Annet->value)
        ->assertDontSee('Synlig butikk');
});

test('guests are redirected from the transaction list', function () {
    $this->get(route('transactions.index'))
        ->assertRedirect(route('login'));
});
