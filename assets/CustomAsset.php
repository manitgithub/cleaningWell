<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Custom Asset Bundle for CleaningWell
 */
class CustomAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
        'css/custom.css',
    ];
    
    public $depends = [
        'app\assets\AdminLteAsset',
    ];
}
