<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = $model->getDisplayName();
$this->params['breadcrumbs'][] = '<i class="fas fa-database"></i> ข้อมูลหลัก';
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-users"></i> จัดการลูกค้า',
    'url' => ['index'],
    'encode' => false,
];
$this->params['breadcrumbs'][] = '<i class="fas fa-user"></i> ' . $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-view">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user"></i> <?= Html::encode($this->title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <?= Html::a('<i class="fas fa-edit"></i> แก้ไข', ['update', 'id' => $model->id], [
                            'class' => 'btn btn-warning',
                            'encode' => false,
                        ]) ?>
                        <?= Html::a('<i class="fas fa-' . ($model->status ? 'ban' : 'check') . '"></i> ' . 
                            ($model->status ? 'ปิดใช้งาน' : 'เปิดใช้งาน'), 
                            ['toggle-status', 'id' => $model->id], 
                            [
                                'class' => 'btn btn-' . ($model->status ? 'warning' : 'success') . ' ml-2',
                                'data-confirm' => 'คุณต้องการเปลี่ยนสถานะลูกค้านี้หรือไม่?',
                                'encode' => false,
                            ]
                        ) ?>
                        <?= Html::a('<i class="fas fa-trash"></i> ลบ', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger ml-2',
                            'data' => [
                                'confirm' => 'คุณต้องการลบลูกค้านี้หรือไม่?',
                                'method' => 'post',
                            ],
                            'encode' => false,
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">ข้อมูลพื้นฐาน</h3>
                        </div>
                        <div class="card-body">
                            <?= DetailView::widget([
                                'model' => $model,
                                'options' => ['class' => 'table table-striped table-bordered detail-view'],
                                'attributes' => [
                                    'name',
                                    'branch',
                                    [
                                        'attribute' => 'customer_type_id',
                                        'value' => $model->getTypeBadge(),
                                        'format' => 'raw',
                                    ],
                                    'contact_name',
                                    [
                                        'attribute' => 'status',
                                        'value' => $model->getStatusBadge(),
                                        'format' => 'raw',
                                    ],
                                ],
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">ข้อมูลติดต่อ</h3>
                        </div>
                        <div class="card-body">
                            <?= DetailView::widget([
                                'model' => $model,
                                'options' => ['class' => 'table table-striped table-bordered detail-view'],
                                'attributes' => [
                                    [
                                        'attribute' => 'phone',
                                        'value' => $model->phone ?: '-',
                                    ],
                                    [
                                        'attribute' => 'email',
                                        'value' => $model->email ? Html::a($model->email, 'mailto:' . $model->email) : '-',
                                        'format' => 'raw',
                                    ],
                                    [
                                        'attribute' => 'tax_id',
                                        'value' => $model->tax_id ?: '-',
                                    ],
                                    [
                                        'attribute' => 'citizen_id',
                                        'value' => $model->citizen_id ?: '-',
                                    ],
                                    [
                                        'attribute' => 'address',
                                        'value' => $model->address ? nl2br(Html::encode($model->address)) : '-',
                                        'format' => 'raw',
                                    ],
                                ],
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">ข้อมูลระบบ</h3>
                        </div>
                        <div class="card-body">
                            <?= DetailView::widget([
                                'model' => $model,
                                'options' => ['class' => 'table table-striped table-bordered detail-view'],
                                'attributes' => [
                                    [
                                        'attribute' => 'created_at',
                                        'value' => date('d/m/Y H:i:s', strtotime($model->created_at)),
                                    ],
                                    [
                                        'attribute' => 'updated_at',
                                        'value' => date('d/m/Y H:i:s', strtotime($model->updated_at)),
                                    ],
                                ],
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
