<?

require_once 'Manager.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Permission.php';


class PermissionManager extends Manager {

    public function hasPermission(User $user, Permission $permission) {

        return true;

    }

}