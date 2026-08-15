<?php

use App\Models\BudgetLine;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected from the budget page', function () {
    $this->get(route('budget.index'))
        ->assertRedirect(route('login'));
});

test('a user can create and update a budget line', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('pages::budget.index')
        ->set('name', 'Strøm')
        ->set('monthly', '1500.00')
        ->set('yearly', '1200.00')
        ->call('save')
        ->assertHasNoErrors();

    $line = $user->budgetLines()->first();

    expect($line)->not->toBeNull()
        ->and($line->name)->toBe('Strøm')
        ->and($line->monthly_nok)->toBe('1600.00');

    Livewire::test('pages::budget.index')
        ->call('edit', $line->id)
        ->set('monthly', '1800.00')
        ->set('yearly', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($line->fresh()->monthly)->toBe('1800.00')
        ->and($line->fresh()->monthly_nok)->toBe('1800.00');
});

test('a user cannot update or delete another users budget line', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $foreign = BudgetLine::factory()->for($other)->create([
        'name' => 'Hemmelig post',
        'monthly' => '100.00',
    ]);

    $this->actingAs($user);

    expect($user->can('update', $foreign))->toBeFalse()
        ->and($user->can('delete', $foreign))->toBeFalse();

    Livewire::test('pages::budget.index')
        ->call('edit', $foreign->id)
        ->assertForbidden();

    Livewire::test('pages::budget.index')
        ->call('delete', $foreign->id)
        ->assertForbidden();

    expect($foreign->fresh())->not->toBeNull()
        ->and($foreign->fresh()->name)->toBe('Hemmelig post');
});
