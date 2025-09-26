<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountEvent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'event_id',
    ];

    /**
     * Get the account that owns the AccountEvent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Accounts::class);
    }

    /**
     * Get the event that owns the AccountEvent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class);
    }
}
