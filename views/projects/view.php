<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Project */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-project-diagram"></i>
                            รายละเอียดโครงการ
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
                                'code:text:รหัสโครงการ',
                                'name:text:ชื่อโครงการ',
                                [
                                    'label' => 'ลูกค้า',
                                    'value' => $model->customer ? $model->customer->name : '-',
                                ],
                                'start_date:date:วันที่เริ่ม',
                                'end_date:date:วันที่สิ้นสุด',
                                [
                                    'label' => 'งบประมาณ',
                                    'value' => $model->getFormattedBudget(),
                                ],
                                [
                                    'label' => 'สถานะ',
                                    'format' => 'raw',
                                    'value' => $model->getStatusBadge(),
                                ],
                                'notes:ntext:หมายเหตุ',
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
                            ข้อมูลเพิ่มเติม
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-user"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">ลูกค้า</span>
                                <span class="info-box-number">
                                    <?= $model->customer ? Html::encode($model->customer->name) : '-' ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($model->customer && $model->customer->contact_name): ?>
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-phone"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">ผู้ติดต่อ</span>
                                <span class="info-box-number">
                                    <?= Html::encode($model->customer->contact_name) ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-money-bill"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">งบประมาณ</span>
                                <span class="info-box-number">
                                    <?= $model->getFormattedBudget() ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($model->start_date && $model->end_date): ?>
                        <div class="info-box">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">ระยะเวลา</span>
                                <span class="info-box-number">
                                    <?php
                                    $start = new DateTime($model->start_date);
                                    $end = new DateTime($model->end_date);
                                    $diff = $start->diff($end);
                                    echo $diff->days . ' วัน';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>
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
                                    'data-confirm' => 'คุณแน่ใจว่าต้องการลบโครงการนี้?'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
