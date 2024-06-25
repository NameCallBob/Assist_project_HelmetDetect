<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'user_id', 'file_name'
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

    /**
     * this func is for save a object in database
     */
    public static function savePic($userId,$file){
        $ob = new Picture();
        $ob -> user_id = $userId;
        $ob -> file_name = $file;
        $ob -> save();
    }
}
