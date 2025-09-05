<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Customer;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'จัดการลูกค้า';
$this->params['breadcrumbs'][] = '<i class="fas fa-database"></i> ข้อมูลหลัก';
$this->params['breadcrumbs'][] = '<i class="fas fa-users"></i> ' . $this->title;
?>
<div class="customers-index">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-users"></i> <?= Html::encode($this->title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <?= Html::a('<i class="fas fa-plus"></i> เพิ่มลูกค้าใหม่', ['create'], [
                            'class' => 'btn btn-success',
                            'encode' => false
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">รายการลูกค้าทั้งหมด</h3>
                </div>
                <div class="card-body">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-bordered table-striped'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            [
                                'attribute' => 'name',
                                'label' => 'ชื่อลูกค้า',
                                'value' => function ($model) {
                                    return Html::a($model->getDisplayName(), ['view', 'id' => $model->id], [
                                        'class' => 'text-primary font-weight-bold'
                                    ]);
                                },
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'customer_type_id',
                                'label' => 'ประเภท',
                                'value' => function ($model) {
                                    return $model->getTypeBadge();
                                },
                                'format' => 'raw',
                                'filter' => \app\models\CustomerType::getTypeOptions(),
                            ],
                            [
                                'attribute' => 'contact_name',
                                'label' => 'ผู้ติดต่อ',
                            ],
                            [
                                'attribute' => 'phone',
                                'label' => 'โทรศัพท์',
                            ],
                            [
                                'attribute' => 'status',
                                'label' => 'สถานะ',
                                'value' => function ($model) {
                                    return $model->getStatusBadge();
                                },
                                'format' => 'raw',
                                'filter' => Customer::getStatusOptions(),
                            ],
                            [
                                'attribute' => 'created_at',
                                'label' => 'วันที่สร้าง',
                                'value' => function ($model) {
                                    return date('d/m/Y H:i', strtotime($model->created_at));
                                },
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'จัดการ',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-eye"></i>', $url, [
                                            'class' => 'btn btn-sm btn-info',
                                            'title' => 'ดูรายละเอียด',
                                        ]);
                                    },
                                    'update' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-edit"></i>', $url, [
                                            'class' => 'btn btn-sm btn-warning',
                                            'title' => 'แก้ไข',
                                        ]);
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                                            'class' => 'btn btn-sm btn-danger',
                                            'title' => 'ลบ',
                                            'data-confirm' => 'คุณต้องการลบลูกค้านี้หรือไม่?',
                                            'data-method' => 'post',
                                        ]);
                                    },
                                ],
                                'contentOptions' => ['style' => 'width: 120px; text-align: center;'],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
