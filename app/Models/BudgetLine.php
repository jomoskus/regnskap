<?php

namespace App\Models;

use App\Enums\Category;
use Database\Factories\BudgetLineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $daily
 * @property string|null $weekly
 * @property string|null $monthly
 * @property string|null $other_monthly
 * @property string|null $yearly
 * @property string|null $note
 * @property-read string $monthly_nok
 */
#[Fillable(['user_id', 'name', 'daily', 'weekly', 'monthly', 'other_monthly', 'yearly', 'note'])]
class BudgetLine extends Model
{
    /** @use HasFactory<BudgetLineFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'daily' => 'decimal:2',
            'weekly' => 'decimal:2',
            'monthly' => 'decimal:2',
            'other_monthly' => 'decimal:2',
            'yearly' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Monthly equivalent: daily×30 + weekly×4.3 + monthly + other_monthly + yearly/12.
     *
     * @return Attribute<numeric-string, never>
     */
    protected function monthlyNok(): Attribute
    {
        return Attribute::get(function (): string {
            $total = ((float) ($this->daily ?? 0)) * 30
                + ((float) ($this->weekly ?? 0)) * 4.3
                + (float) ($this->monthly ?? 0)
                + (float) ($this->other_monthly ?? 0)
                + ((float) ($this->yearly ?? 0)) / 12;

            return number_format($total, 2, '.', '');
        });
    }

    public function mappedCategory(): ?Category
    {
        return Category::tryFromLabel($this->name);
    }
}
