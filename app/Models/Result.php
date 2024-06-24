<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'picture_id', 'analysis'
    ];

    // 定义关联关系
    public function picture()
    {
        return $this->belongsTo(Picture::class);
    }
}
