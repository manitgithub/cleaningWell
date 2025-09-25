<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ใบเสนอราคา';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-index">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-plus"></i> สร้างใบเสนอราคา', ['create'], ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <div class="card-body">
            <?php Pjax::begin(); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'code',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::a($model->code, ['view', 'id' => $model->id], ['class' => 'text-primary']);
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'format' => 'date',
                    ],
                    [
                        'attribute' => 'project_id',
                        'value' => function($model) {
                            return $model->project ? $model->project->name : '-';
                        }
                    ],
                    [
                        'attribute' => 'customer_id',
                        'value' => function($model) {
                            return $model->customer ? $model->customer->name : '-';
                        }
                    ],
                    'subject',
                    [
                        'attribute' => 'grand_total',
                        'format' => ['decimal', 2],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->status == 1) {
                                return '<span class="badge badge-success">ใช้งาน</span>';
                            } else {
                                return '<span class="badge badge-secondary">ปิดใช้งาน</span>';
                            }
                        },
                        'contentOptions' => ['class' => 'text-center'],
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {toggle-status} {delete}',
                        'buttons' => [
                            'toggle-status' => function ($url, $model, $key) {
                                $icon = $model->status == 1 ? 'fa-toggle-off' : 'fa-toggle-on';
                                $title = $model->status == 1 ? 'ปิดใช้งาน' : 'เปิดใช้งาน';
                                return Html::a('<i class="fas ' . $icon . '"></i>', $url, [
                                    'title' => $title,
                                    'data-method' => 'post',
                                    'data-confirm' => 'คุณต้องการเปลี่ยนสถานะหรือไม่?',
                                    'class' => 'btn btn-sm btn-outline-warning'
                                ]);
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-eye"></i>', $url, [
                                    'title' => 'ดู',
                                    'class' => 'btn btn-sm btn-outline-info'
                                ]);
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-edit"></i>', $url, [
                                    'title' => 'แก้ไข',
                                    'class' => 'btn btn-sm btn-outline-primary'
                                ]);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-trash"></i>', $url, [
                                    'title' => 'ลบ',
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'data-method' => 'post',
                                    'data-confirm' => 'คุณต้องการลบรายการนี้หรือไม่?',
                                ]);
                            },
                        ],
                        'contentOptions' => ['style' => 'width: 200px; text-align: center;'],
                    ],
                ],
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination justify-content-center'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>

</div>
