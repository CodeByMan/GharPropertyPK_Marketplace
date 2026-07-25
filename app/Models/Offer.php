<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory;

    protected $casts = [
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected $fillable = [
        'listing_id',
        'bidder_id',
        'amount',
        'accepted_at',
        'rejected_at',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function bidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bidder_id');
    }

    public function scopeExcept(Builder $query, Offer $offer): Builder
    {
        return $query->where('id', '!=', $offer->id);
    }

    public function scopeByMe(Builder $query): Builder
    {
        return $query->where('bidder_id', auth()->id());
    }
}
