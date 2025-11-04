<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'model_number',
        'description',
    ];

    /**
     * A product can be owned by many users through warranties.
     */
    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }
}
