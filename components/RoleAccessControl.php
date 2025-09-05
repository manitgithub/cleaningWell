<?php

namespace app\components;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

/**
 * AccessControl filter for role-based access
 */
class RoleAccessControl extends AccessControl
{
    /**
     * @var array roles that are allowed to access the action
     */
    public $roles = [];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $user = Yii::$app->user;
        
        // If user is not logged in, redirect to login
        if ($user->isGuest) {
            if ($this->owner instanceof \yii\web\Controller) {
                return $this->owner->redirect(['/site/login']);
            }
            return false;
        }

        // Check role access
        if (!empty($this->roles)) {
            $userRole = $user->identity->role;
            if (!in_array($userRole, $this->roles)) {
                throw new ForbiddenHttpException('คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
            }
        }

        return true; // Allow access if all checks pass
    }
}
