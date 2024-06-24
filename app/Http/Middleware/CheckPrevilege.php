<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\ActionModel;
use Illuminate\Http\Request;

class CheckPrevilege {
    protected $userModel;
    protected $actionModel;

    public function __construct(User $userModel, ActionModel $actionModel) {
        $this->userModel = $userModel;
        $this->actionModel = $actionModel;
    }

    public function handle(Request $request, Closure $next) {
        $user_phone = $request->input('token_phone');
        $routeAction = $request->route()[1]['as'] ?? '';

        // 调试输出
        error_log("User phone: $user_phone, Action: $routeAction", 3, '/path/to/your/custom.log');

        // 使用 User 模型获取用户的角色
        $userRoles = $this->userModel->getRoles($user_phone);
        error_log("User roles: " . json_encode($userRoles), 3, '/path/to/your/custom.log');

        // 使用 ActionModel 模型获取操作的角色
        $actionRoles = $this->actionModel->getRoles($routeAction);
        error_log("Action roles: " . json_encode($actionRoles), 3, '/path/to/your/custom.log');

        // 检查用户角色和操作角色的交集是否大于 0
        if (count(array_intersect($userRoles, $actionRoles)) > 0) {
            return $next($request);
        } else {
            return response('权限不足', 401);
        }
    }
}




