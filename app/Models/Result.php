<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'picture_id', 'analysis' , "result_file_name"
    ];

    // 定义关联关系
    public function picture()
    {
        return $this->belongsTo(Picture::class);
    }

    // 新增
    public static function saveResult($pic,$ana,$file = null){
        $ob = new Result();
        $ob -> picture_id = $pic;
        $ob -> analysis = $ana;
        $ob -> result_file_name = $file;
        $ob -> save();
    }
}
