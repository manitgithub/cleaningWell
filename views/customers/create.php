<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'เพิ่มลูกค้าใหม่';
$this->params['breadcrumbs'][] = '<i class="fas fa-database"></i> ข้อมูลหลัก';
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-users"></i> จัดการลูกค้า',
    'url' => ['index'],
    'encode' => false,
];
$this->params['breadcrumbs'][] = '<i class="fas fa-plus"></i> ' . $this->title;
?>
<div class="customer-create">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1><i class="fas fa-plus"></i> <?= Html::encode($this->title) ?></h1>
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
