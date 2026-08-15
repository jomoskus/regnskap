<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Confidence;
use Database\Factories\CategoryRuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $payee
 * @property Category $category
 * @property Confidence $confidence
 * @property int $matches
 */
#[Fillable(['payee', 'category', 'confidence', 'matches'])]
class CategoryRule extends Model
{
    /** @use HasFactory<CategoryRuleFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => Category::class,
            'confidence' => Confidence::class,
            'matches' => 'integer',
        ];
    }

    /**
     * Suggest a category for a payee. Never auto-applies; callers only display it.
     * Exact payee wins, then prefix/token (so "AtB Billett" can match "AtB").
     * Rules marked uklart are never suggested.
     */
    public static function suggestFor(string $payee): ?self
    {
        $payee = trim($payee);

        if ($payee === '') {
            return null;
        }

        $rules = static::query()
            ->whereIn('confidence', [Confidence::Opplagt, Confidence::Sannsynlig])
            ->get();

        $exact = $rules->first(
            fn (self $rule): bool => mb_strtolower($rule->payee) === mb_strtolower($payee),
        );

        if ($exact instanceof self) {
            return $exact;
        }

        return $rules
            ->filter(fn (self $rule): bool => static::payeeMatches($payee, $rule->payee))
            ->sortBy([
                fn (self $rule): int => $rule->confidence === Confidence::Opplagt ? 0 : 1,
                fn (self $rule): int => -mb_strlen($rule->payee),
                fn (self $rule): int => -$rule->matches,
            ])
            ->first();
    }

    public static function payeeMatches(string $payee, string $rulePayee): bool
    {
        $payeeNorm = mb_strtolower(trim($payee));
        $ruleNorm = mb_strtolower(trim($rulePayee));

        if ($payeeNorm === '' || $ruleNorm === '') {
            return false;
        }

        if ($payeeNorm === $ruleNorm) {
            return true;
        }

        if (str_starts_with($payeeNorm, $ruleNorm)) {
            $boundary = mb_substr($payeeNorm, mb_strlen($ruleNorm), 1);

            if ($boundary === '' || preg_match('/\s/u', $boundary) === 1) {
                return true;
            }
        }

        $tokens = preg_split('/\s+/u', $payeeNorm) ?: [];

        return in_array($ruleNorm, $tokens, true);
    }
}
