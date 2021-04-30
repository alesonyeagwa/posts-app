<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'published_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];


    /** Begin Relationships */

    public function author(){
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    /** End Relationships */

    /** Begin Scopes */
    
    public function scopePublished($query){
        return $query->where('published_at', '!=', null);
    }

    /** End Scopes */


    public function publish(){
        if($this->published_at == null){
            $this->published_at = \Carbon\Carbon::now();
            $this->save();
        }
    }

    public function isPublished(){
        return $this->published_at !== null;
    }
    
}
