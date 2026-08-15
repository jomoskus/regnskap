<?php

namespace App\Models;

use App\Enums\FigureSection;
use Database\Factories\MonthlyFigureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property Carbon $month
 * @property FigureSection $section
 * @property string $item
 * @property string $amount
 * @property string|null $note
 */
#[Fillable(['user_id', 'month', 'section', 'item', 'amount', 'note'])]
class MonthlyFigure extends Model
{
    /** @use HasFactory<MonthlyFigureFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'month' => 'date',
            'section' => FigureSection::class,
            'amount' => 'decimal:2',
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
