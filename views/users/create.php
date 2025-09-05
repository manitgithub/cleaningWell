<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'เพิ่มผู้ใช้ใหม่';
$this->params['breadcrumbs'][] = '<i class="fas fa-cog"></i> ระบบ';
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-users"></i> จัดการผู้ใช้งาน', 
    'url' => ['index'],
    'encode' => false,
];
$this->params['breadcrumbs'][] = '<i class="fas fa-user-plus"></i> ' . $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
