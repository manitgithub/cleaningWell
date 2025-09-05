<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * AdminLTE 3 Asset Bundle (CDN)
 */
class AdminLteAsset extends AssetBundle
{
    public $css = [
        'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css',
    ];
    
    public $js = [
        'https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'app\assets\JQueryAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
