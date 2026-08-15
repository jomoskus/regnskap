<?php

use App\Enums\Category;
use App\Models\BudgetLine;

test('monthly equivalent combines daily weekly monthly and yearly fields', function () {
    $line = BudgetLine::factory()->make([
        'name' => 'Dagligvarer',
        'daily' => '10.00',
        'weekly' => '10.00',
        'monthly' => '100.00',
        'other_monthly' => '20.00',
        'yearly' => '120.00',
    ]);

    expect($line->monthly_nok)->toBe('473.00')
        ->and($line->mappedCategory())->toBe(Category::Dagligvarer);
});

test('extra envelopes that are not categories stay unmapped', function () {
    $line = BudgetLine::factory()->make([
        'name' => 'Strøm',
        'monthly' => '200.00',
    ]);

    expect($line->mappedCategory())->toBeNull();
});
