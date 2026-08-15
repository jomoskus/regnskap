<?php

namespace App\Models;

use App\Enums\RecurringInterval;
use Database\Factories\RecurringCostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $amount
 * @property string $currency
 * @property string $monthly_nok
 * @property Carbon|null $renews_on
 * @property RecurringInterval $interval
 * @property string|null $payment_method
 * @property string|null $note
 */
#[Fillable(['user_id', 'name', 'amount', 'currency', 'monthly_nok', 'renews_on', 'interval', 'payment_method', 'note'])]
class RecurringCost extends Model
{
    /** @use HasFactory<RecurringCostFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'monthly_nok' => 'decimal:2',
            'renews_on' => 'date',
            'interval' => RecurringInterval::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function monthlyEquivalent(string $amount, RecurringInterval $interval): string
    {
        return number_format(((float) $amount) * $interval->toMonthlyFactor(), 2, '.', '');
    }
}
