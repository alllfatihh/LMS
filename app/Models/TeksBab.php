<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeksBab extends Model
{
    protected $table = 'teks_bab';

    protected $fillable = ['bab_id', 'content'];

    public function bab(): BelongsTo
    {
        return $this->belongsTo(Bab::class, 'bab_id');
    }
}
