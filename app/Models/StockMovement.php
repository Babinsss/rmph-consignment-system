<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_RECEIVE = 'RECEIVE';

    public const TYPE_ISSUE = 'ISSUE';

    public const TYPE_ADJUST = 'ADJUST';

    protected $fillable = [
        'batch_id',
        'user_id',
        'type',
        'quantity',
        'balance_after',
        'movement_date',
        'reference_number',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'movement_date' => 'date',
            'quantity' => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
