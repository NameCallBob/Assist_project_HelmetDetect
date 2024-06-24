<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ActionModel {
    public function getRoles($action_name) {
        $sql = "select role_id from role_action where action_id = (select id from actions where name = ?)";
        $response = DB::select($sql, [$action_name]);
        error_log("ActionModel getRoles response: " . json_encode($response), 3, '/path/to/your/custom.log');
        return array_column($response, 'role_id');
    }
    
}