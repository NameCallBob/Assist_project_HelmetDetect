<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'user_id', 'url'
    ];

    // 定义关联关系
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
