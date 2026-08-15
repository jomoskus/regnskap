<?php

namespace App\Models;

use Database\Factories\HousingPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $horizon_year
 * @property string|null $sale_price
 * @property string|null $mortgage_on_sold
 * @property string|null $equity_from_sale
 * @property string|null $saving_per_year
 * @property string|null $saved_total
 * @property string|null $expected_income
 * @property string|null $possible_loan
 * @property string|null $student_loan
 * @property string|null $mortgage
 * @property string|null $purchase_price
 */
#[Fillable([
    'user_id',
    'horizon_year',
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
])]
class HousingPlan extends Model
{
    /** @use HasFactory<HousingPlanFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'horizon_year' => 'integer',
            'sale_price' => 'decimal:2',
            'mortgage_on_sold' => 'decimal:2',
            'equity_from_sale' => 'decimal:2',
            'saving_per_year' => 'decimal:2',
            'saved_total' => 'decimal:2',
            'expected_income' => 'decimal:2',
            'possible_loan' => 'decimal:2',
            'student_loan' => 'decimal:2',
            'mortgage' => 'decimal:2',
            'purchase_price' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
