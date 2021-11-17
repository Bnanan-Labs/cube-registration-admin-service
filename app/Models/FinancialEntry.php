<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FinancialBook;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class FinancialEntry extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(FinancialBook::class, 'book_id');
    }
}
