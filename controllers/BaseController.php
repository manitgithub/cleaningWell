<?php

namespace app\controllers;

use yii\web\Controller;

/**
 * Base Controller with AdminLTE layout
 */
class BaseController extends Controller
{
    public $layout = 'adminlte';
    
    public function init()
    {
        parent::init();
        
        // Force login for all pages except login page
        if (\Yii::$app->user->isGuest && $this->action->id !== 'login') {
            return $this->redirect(['/site/login']);
        }
    }
}
