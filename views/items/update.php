<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Item */

$this->title = 'แก้ไขสินค้า/บริการ: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><?= Html::a('Home', ['/site/index']) ?></li>
                    <li class="breadcrumb-item"><?= Html::a('Items', ['index']) ?></li>
                    <li class="breadcrumb-item"><?= Html::a($model->name, ['view', 'id' => $model->id]) ?></li>
                    <li class="breadcrumb-item active">แก้ไข</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>
                    แก้ไขสินค้า/บริการ
                </h3>
                <div class="card-tools">
                    <?= Html::a('<i class="fas fa-eye"></i> ดูรายละเอียด', ['view', 'id' => $model->id], [
                        'class' => 'btn btn-info btn-sm'
                    ]) ?>
                    <?= Html::a('<i class="fas fa-list"></i> กลับไปรายการ', ['index'], [
                        'class' => 'btn btn-secondary btn-sm'
                    ]) ?>
                </div>
            </div>
            <div class="card-body">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</section>
