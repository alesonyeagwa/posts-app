<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'commenter_id',
        'post_id',
        'comment',
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

    public function commenter(){
        return $this->belongsTo(User::class, 'commenter_id');
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }
}
