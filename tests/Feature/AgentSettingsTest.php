<?php

use App\Models\User;
use Livewire\Livewire;

test('guests are redirected from the agent settings page', function () {
    $this->get(route('settings.agent'))
        ->assertRedirect(route('login'));
});

test('the agent settings page can create and revoke a token', function () {
    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $this->get(route('settings.agent'))->assertOk();

    $page = Livewire::test('pages::settings.agent')
        ->assertSet('name', 'Personlig regnskapsfører')
        ->call('createToken')
        ->assertHasNoErrors();

    expect($page->get('plainTextToken'))->toBeString()->not->toBeEmpty()
        ->and($user->tokens()->count())->toBe(1)
        ->and($user->tokens()->first()->name)->toBe('Personlig regnskapsfører');

    $id = $user->tokens()->first()->id;

    Livewire::test('pages::settings.agent')
        ->call('revoke', $id)
        ->assertHasNoErrors()
        ->assertSet('plainTextToken', null);

    expect($user->tokens()->count())->toBe(0);
});
