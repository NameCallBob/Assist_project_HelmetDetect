<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\Model;

// 外model
use App\Models\Picture;
use App\Models\Comment;
use App\Models\Role;

class User extends Model implements AuthenticatableContract, JWTSubject
{
    protected $table = 'users';
    // 資料庫欄位
    protected $fillable = [
        'name', 'account', 'email', 'password','phone'
    ];

    // 關聯关系
    public function pictures()
    {
        return $this->hasMany(Picture::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this -> id,
            // 添加您需要的其他自定義聲明
        ];
    }
    public function getAuthIdentifierName()
    {
        return 'account';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
    // Custom SQL



    public function updateUser($name, $phone, $account, $email)
    {
        $sql = "update users set name = :name, phone = :phone, email = :email where account = :account";
        $response = DB::update($sql, [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'account' => $account
        ]);
        return $response;
    }


    public function register($name, $phone, $account, $email, $password)
    {
        $sql = "insert into users (name, phone, account, email, password) values (:name, :phone, :account, :email, :password)";
        
        try {
            $response = DB::insert($sql, [
                'name' => $name,
                'phone' => $phone,
                'account' => $account,
                'email' => $email,
                'password' => $password
            ]);
            return $response;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Database insert error: ' . $e->getMessage());
            return false;
        }
    }


    public function getRoles($user_phone) {
        $sql = "select role_id from user_role where user_phone = ?";
        $response = DB::select($sql, [$user_phone]);
        error_log("getRoles response: " . json_encode($response), 3, '/path/to/your/custom.log');
        return array_column($response, 'role_id');
    }


    public function readimg($date)
    {
        $sql = "SELECT image FROM image WHERE date = ?";
        $response = DB::select($sql, [$date]);
    
        // 返回原始的圖片數據
        $images = [];
        foreach ($response as $row) {
            $images[] = $row->image;
        }
    
        return $images;
    }




    private function addImageToDatabase($imageData) {
        try {
            // 执行数据库插入操作
            $sql = "INSERT INTO image_table (image_column) VALUES (?)";
            DB::insert($sql, [$imageData]);

            return true; // 成功插入返回 true
        } catch (\Exception $e) {
            // 处理插入失败的情况
            return false;
        }
    }
}
