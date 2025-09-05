<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items';
$this->params['breadcrumbs'][] = $this->title;
?>



<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-boxes"></i>
                    จัดการสินค้า/บริการ
                </h3>
                <div class="card-tools">
                    <?= Html::a('<i class="fas fa-plus"></i> เพิ่มสินค้า/บริการ', ['create'], [
                        'class' => 'btn btn-primary btn-sm'
                    ]) ?>
                </div>
            </div>
            <div class="card-body">
                <?php Pjax::begin(); ?>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => ['class' => 'table table-striped table-bordered'],
                    'layout' => "{summary}\n{items}\n{pager}",
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        
                        [
                            'attribute' => 'name',
                            'label' => 'ชื่อสินค้า/บริการ',
                            'format' => 'text',
                        ],
                        
                        [
                            'attribute' => 'unit',
                            'label' => 'หน่วย',
                            'format' => 'text',
                            'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
                        ],
                        
                        [
                            'attribute' => 'base_price',
                            'label' => 'ราคาหน่วย',
                            'format' => 'text',
                            'value' => function($model) {
                                return $model->getFormattedPrice();
                            },
                            'contentOptions' => ['style' => 'width: 120px; text-align: right;'],
                        ],
                        
                        [
                            'attribute' => 'vat_applicable',
                            'label' => 'VAT',
                            'format' => 'raw',
                            'value' => function($model) {
                                return $model->getVatBadge();
                            },
                            'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
                        ],
                        
                        [
                            'attribute' => 'wht_default',
                            'label' => 'หัก ณ ที่จ่าย (%)',
                            'format' => 'text',
                            'value' => function($model) {
                                return number_format($model->wht_default, 2) . '%';
                            },
                            'contentOptions' => ['style' => 'width: 100px; text-align: center;'],
                        ],
                        
                        [
                            'attribute' => 'is_active',
                            'label' => 'สถานะ',
                            'format' => 'raw',
                            'value' => function($model) {
                                return $model->getStatusBadge();
                            },
                            'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => 'จัดการ',
                            'headerOptions' => ['style' => 'width: 120px; text-align: center;'],
                            'contentOptions' => ['style' => 'text-align: center;'],
                            'template' => '{view} {update} {toggle} {delete}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-eye"></i>', $url, [
                                        'title' => 'ดูรายละเอียด',
                                        'class' => 'btn btn-info btn-xs',
                                        'style' => 'margin: 1px;'
                                    ]);
                                },
                                'update' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-edit"></i>', $url, [
                                        'title' => 'แก้ไข',
                                        'class' => 'btn btn-warning btn-xs',
                                        'style' => 'margin: 1px;'
                                    ]);
                                },
                                'toggle' => function ($url, $model, $key) {
                                    $icon = $model->is_active == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                                    $class = $model->is_active == 1 ? 'btn-success' : 'btn-secondary';
                                    return Html::a('<i class="fas ' . $icon . '"></i>', ['toggle-status', 'id' => $model->id], [
                                        'title' => 'เปลี่ยนสถานะ',
                                        'class' => 'btn ' . $class . ' btn-xs',
                                        'style' => 'margin: 1px;',
                                        'data-method' => 'post',
                                        'data-confirm' => 'คุณต้องการเปลี่ยนสถานะสินค้า/บริการนี้หรือไม่?'
                                    ]);
                                },
                                'delete' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-trash"></i>', $url, [
                                        'title' => 'ลบ',
                                        'class' => 'btn btn-danger btn-xs',
                                        'style' => 'margin: 1px;',
                                        'data-method' => 'post',
                                        'data-confirm' => 'คุณแน่ใจว่าต้องการลบสินค้า/บริการนี้?'
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>

                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</section>
