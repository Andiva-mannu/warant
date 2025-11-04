<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'serial_number',
        'purchase_date',
        'duration_months',
        'provider',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the user who owns this warranty.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product this warranty is for.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
