<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'แก้ไขผู้ใช้: ' . $model->username;
$this->params['breadcrumbs'][] = '<i class="fas fa-cog"></i> ระบบ';
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-users"></i> จัดการผู้ใช้งาน', 
    'url' => ['index'],
    'encode' => false,
];
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-user"></i> ' . ($model->display_name ?: $model->username), 
    'url' => ['view', 'id' => $model->id],
    'encode' => false,
];
$this->params['breadcrumbs'][] = '<i class="fas fa-edit"></i> แก้ไข';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
