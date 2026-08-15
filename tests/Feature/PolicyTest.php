<?php

use App\Models\Account;
use App\Models\Holding;
use App\Models\HousingPlan;
use App\Models\MonthlyFigure;
use App\Models\RecurringCost;
use App\Models\User;
use Livewire\Livewire;

test('policies prevent other users from changing owned rows', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $figure = MonthlyFigure::factory()->for($other)->create(['item' => 'Kontanter test']);
    $cost = RecurringCost::factory()->for($other)->create(['name' => 'Hemmelig abonnement']);
    $holding = Holding::factory()->for($other)->create(['name' => 'Hemmelig fond']);
    $plan = HousingPlan::factory()->for($other)->create(['horizon_year' => 2029]);
    $account = Account::factory()->for($other)->create(['name' => 'Hemmelig konto']);

    $this->actingAs($user);

    foreach ([$figure, $cost, $holding, $plan, $account] as $model) {
        expect($user->can('view', $model))->toBeFalse()
            ->and($user->can('update', $model))->toBeFalse()
            ->and($user->can('delete', $model))->toBeFalse();
    }

    Livewire::test('pages::wealth.index')
        ->call('delete', $figure->id)
        ->assertForbidden();

    Livewire::test('pages::recurring.index')
        ->call('delete', $cost->id)
        ->assertForbidden();

    Livewire::test('pages::investments.index')
        ->call('delete', $holding->id)
        ->assertForbidden();

    Livewire::test('pages::housing.index')
        ->call('delete', $plan->id)
        ->assertForbidden();

    Livewire::test('pages::accounts.index')
        ->call('delete', $account->id)
        ->assertForbidden();

    expect($figure->fresh())->not->toBeNull()
        ->and($cost->fresh())->not->toBeNull()
        ->and($holding->fresh())->not->toBeNull()
        ->and($plan->fresh())->not->toBeNull()
        ->and($account->fresh())->not->toBeNull();
});

test('a user can manage their own wealth recurring holding housing and account rows', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('pages::wealth.index')
        ->set('section', 'formue')
        ->set('item', 'Kontanter eksempel')
        ->set('amount', '250.00')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::test('pages::recurring.index')
        ->set('name', 'Testabonnement')
        ->set('amount', '99.00')
        ->set('interval', 'monthly')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::test('pages::investments.index')
        ->set('name', 'Testfond')
        ->set('value', '500.00')
        ->set('type', 'portefolje')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::test('pages::housing.index')
        ->set('horizon_year', '2030')
        ->set('purchase_price', '100000.00')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::test('pages::accounts.index')
        ->set('name', 'Testkonto')
        ->set('type', 'brukskonto')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->monthlyFigures()->where('item', 'Kontanter eksempel')->exists())->toBeTrue()
        ->and($user->recurringCosts()->where('name', 'Testabonnement')->exists())->toBeTrue()
        ->and($user->holdings()->where('name', 'Testfond')->exists())->toBeTrue()
        ->and($user->housingPlans()->where('horizon_year', 2030)->exists())->toBeTrue()
        ->and($user->accounts()->where('name', 'Testkonto')->exists())->toBeTrue();
});
