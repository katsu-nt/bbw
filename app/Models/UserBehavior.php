<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBehavior extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_behavior';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'publisherId',
        'action'
    ];

    /**
     * Get the publisher associated with the behavior.
     */
    public function publisher()
    {
        // Assuming you have a Publisher model
        // If not, you can implement this relationship later
        // return $this->belongsTo(Publisher::class, 'publisherId');
    }
}
