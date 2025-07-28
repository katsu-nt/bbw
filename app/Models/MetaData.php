<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MetaData extends Model
{
    // Specify the table name (optional if it matches Laravel's plural convention)
    protected $table = 'meta_data';

    // Disable auto-incrementing because you're using UUID
    public $incrementing = false;

    // Disable timestamps if not using created_at / updated_at
    public $timestamps = false;

    // Define which attributes are mass assignable
    protected $fillable = [
        'publisherId',
        'title',
        'author',
        'keywords',
        'description',
        'og_image',
        'og_url',
        'namechannel',
        'cateslug',
    ];
}
