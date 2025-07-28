<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleMetaData extends Model
{
    protected $table = 'article_meta_data';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'publisherId',
        'title',
        'author',
        'keywords',
        'description',
        'content',
        'og_image',
        'og_url',
        'namechannel',
        'cateslug',
        'summarize',
        'audio',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
