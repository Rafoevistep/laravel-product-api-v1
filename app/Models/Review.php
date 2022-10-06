<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * @var mixed
     */

    protected  $fillable = [
        'review',
        'rating',
        'user_id',
        'product_id'

    ];
     /**
     * Get the product that owns the review.
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    /**
     * Get the user that made the review.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
