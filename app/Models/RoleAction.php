<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAction extends Model
{
    protected $table = 'role_action';
    protected $fillable = ['role_id', 'action_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
