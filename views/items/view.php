<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Item */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-boxes"></i>
                            รายละเอียดสินค้า/บริการ
                        </h3>
                        <div class="card-tools">
                            <?= Html::a('<i class="fas fa-edit"></i> แก้ไข', ['update', 'id' => $model->id], [
                                'class' => 'btn btn-warning btn-sm'
                            ]) ?>
                            <?= Html::a('<i class="fas fa-list"></i> กลับไปรายการ', ['index'], [
                                'class' => 'btn btn-secondary btn-sm'
                            ]) ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'options' => ['class' => 'table table-striped table-bordered detail-view'],
                            'attributes' => [
                                'name:text:ชื่อสินค้า/บริการ',
                                'unit:text:หน่วย',
                                [
                                    'label' => 'ราคาหน่วย',
                                    'value' => $model->getFormattedPrice(),
                                ],
                                [
                                    'label' => 'คิด VAT',
                                    'format' => 'raw',
                                    'value' => $model->getVatBadge(),
                                ],
                                [
                                    'label' => 'หัก ณ ที่จ่าย (%)',
                                    'value' => number_format($model->wht_default, 2) . '%',
                                ],
                                [
                                    'label' => 'สถานะ',
                                    'format' => 'raw',
                                    'value' => $model->getStatusBadge(),
                                ],
                                'created_at:datetime:วันที่สร้าง',
                                'updated_at:datetime:วันที่แก้ไข',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            ข้อมูลสรุป
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-tag"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">ราคาหน่วย</span>
                                <span class="info-box-number">
                                    <?= $model->getFormattedPrice() ?>
                                </span>
                            </div>
                        </div>

                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-balance-scale"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">หน่วยนับ</span>
                                <span class="info-box-number">
                                    <?= Html::encode($model->unit) ?>
                                </span>
                            </div>
                        </div>

                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-percent"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">หัก ณ ที่จ่าย</span>
                                <span class="info-box-number">
                                    <?= number_format($model->wht_default, 2) ?>%
                                </span>
                            </div>
                        </div>

                        <div class="info-box">
                            <span class="info-box-icon <?= $model->vat_applicable ? 'bg-success' : 'bg-secondary' ?>">
                                <i class="fas fa-receipt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">VAT</span>
                                <span class="info-box-number">
                                    <?= $model->vat_applicable ? 'คิด VAT' : 'ไม่คิด VAT' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <?= Html::a('<i class="fas fa-edit"></i> แก้ไข', ['update', 'id' => $model->id], [
                                    'class' => 'btn btn-warning btn-block btn-sm'
                                ]) ?>
                            </div>
                            <div class="col-6">
                                <?= Html::a('<i class="fas fa-trash"></i> ลบ', ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger btn-block btn-sm',
                                    'data-method' => 'post',
                                    'data-confirm' => 'คุณแน่ใจว่าต้องการลบสินค้า/บริการนี้?'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
