<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'แก้ไขลูกค้า: ' . $model->name;
$this->params['breadcrumbs'][] = '<i class="fas fa-database"></i> ข้อมูลหลัก';
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-users"></i> จัดการลูกค้า',
    'url' => ['index'],
    'encode' => false,
];
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-user"></i> ' . $model->name,
    'url' => ['view', 'id' => $model->id],
    'encode' => false,
];
$this->params['breadcrumbs'][] = '<i class="fas fa-edit"></i> แก้ไข';
?>
<div class="customer-update">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1><i class="fas fa-edit"></i> <?= Html::encode($this->title) ?></h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>

</div>
