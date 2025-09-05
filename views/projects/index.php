<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
?>


<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-project-diagram"></i>
                    จัดการโครงการ
                </h3>
                <div class="card-tools">
                    <?= Html::a('<i class="fas fa-plus"></i> เพิ่มโครงการใหม่', ['create'], [
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
                            'attribute' => 'code',
                            'label' => 'รหัสโครงการ',
                            'format' => 'text',
                            'contentOptions' => ['style' => 'width: 120px;'],
                        ],
                        
                        [
                            'attribute' => 'name',
                            'label' => 'ชื่อโครงการ',
                            'format' => 'text',
                        ],
                        
                        [
                            'attribute' => 'customer.name',
                            'label' => 'ลูกค้า',
                            'format' => 'text',
                            'value' => function($model) {
                                return $model->customer ? $model->customer->name : '-';
                            }
                        ],
                        
                        [
                            'attribute' => 'start_date',
                            'label' => 'วันที่เริ่ม',
                            'format' => 'date',
                            'contentOptions' => ['style' => 'width: 100px;'],
                        ],
                        
                        [
                            'attribute' => 'end_date',
                            'label' => 'วันที่สิ้นสุด',
                            'format' => 'date',
                            'contentOptions' => ['style' => 'width: 100px;'],
                        ],
                        
                        [
                            'attribute' => 'budget',
                            'label' => 'งบประมาณ',
                            'format' => 'text',
                            'value' => function($model) {
                                return $model->getFormattedBudget();
                            },
                            'contentOptions' => ['style' => 'width: 120px; text-align: right;'],
                        ],
                        
                        [
                            'attribute' => 'status',
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
                                    $icon = $model->status == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
                                    $class = $model->status == 1 ? 'btn-success' : 'btn-secondary';
                                    return Html::a('<i class="fas ' . $icon . '"></i>', ['toggle-status', 'id' => $model->id], [
                                        'title' => 'เปลี่ยนสถานะ',
                                        'class' => 'btn ' . $class . ' btn-xs',
                                        'style' => 'margin: 1px;',
                                        'data-method' => 'post',
                                        'data-confirm' => 'คุณต้องการเปลี่ยนสถานะโครงการนี้หรือไม่?'
                                    ]);
                                },
                                'delete' => function ($url, $model, $key) {
                                    return Html::a('<i class="fas fa-trash"></i>', $url, [
                                        'title' => 'ลบ',
                                        'class' => 'btn btn-danger btn-xs',
                                        'style' => 'margin: 1px;',
                                        'data-method' => 'post',
                                        'data-confirm' => 'คุณแน่ใจว่าต้องการลบโครงการนี้?'
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

<?php
$this->registerJs("
    // Auto format numbers
    function formatNumber(input) {
        let value = input.value.replace(/[^\d.]/g, '');
        if (value) {
            input.value = parseFloat(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
");
?>
