<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'จัดการผู้ใช้งาน';
$this->params['breadcrumbs'][] = '<i class="fas fa-cog"></i> ระบบ';
$this->params['breadcrumbs'][] = '<i class="fas fa-users"></i> ' . $this->title;
?>
<div class="user-index">

    <!-- Main card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users mr-2"></i>
                <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-plus mr-1"></i> เพิ่มผู้ใช้ใหม่', ['create'], [
                    'class' => 'btn btn-success btn-sm'
                ]) ?>
            </div>
        </div>
        <!-- /.card-header -->
        
        <div class="card-body table-responsive p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-hover text-nowrap'],
                'summary' => '<div class="text-muted ml-3 mt-3">แสดง {begin}-{end} จาก {totalCount} รายการ</div>',
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width: 50px'],
                    ],

                    [
                        'attribute' => 'username',
                        'label' => 'ชื่อผู้ใช้',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::tag('span', Html::encode($model->username), [
                                'class' => 'badge badge-info'
                            ]);
                        },
                        'headerOptions' => ['style' => 'width: 120px'],
                    ],
                    
                    [
                        'attribute' => 'display_name',
                        'label' => 'ชื่อแสดง',
                        'value' => function($model) {
                            return $model->display_name ?: '-';
                        },
                    ],
                    
                    [
                        'attribute' => 'email',
                        'label' => 'อีเมล',
                        'format' => 'email',
                        'value' => function($model) {
                            return $model->email ?: '-';
                        },
                    ],
                    
                    [
                        'attribute' => 'role',
                        'label' => 'บทบาท',
                        'format' => 'raw',
                        'value' => function($model) {
                            $class = $model->role === User::ROLE_ADMIN ? 'badge-danger' : 'badge-primary';
                            return Html::tag('span', $model->getRoleName(), [
                                'class' => "badge {$class}"
                            ]);
                        },
                        'filter' => [
                            User::ROLE_ADMIN => 'ผู้ดูแลระบบ',
                            User::ROLE_HOUSEKEEPER => 'แม่บ้าน',
                        ],
                        'headerOptions' => ['style' => 'width: 120px'],
                    ],
                    
                    [
                        'attribute' => 'status',
                        'label' => 'สถานะ',
                        'format' => 'raw',
                        'value' => function($model) {
                            $class = $model->status === User::STATUS_ACTIVE ? 'badge-success' : 'badge-secondary';
                            $icon = $model->status === User::STATUS_ACTIVE ? 'fa-check' : 'fa-times';
                            return Html::tag('span', 
                                '<i class="fas ' . $icon . ' mr-1"></i>' . $model->getStatusName(), 
                                ['class' => "badge {$class}"]
                            );
                        },
                        'filter' => [
                            User::STATUS_ACTIVE => 'ใช้งาน',
                            User::STATUS_INACTIVE => 'ไม่ใช้งาน',
                        ],
                        'headerOptions' => ['style' => 'width: 100px'],
                    ],
                    
                    [
                        'attribute' => 'last_login_at',
                        'label' => 'เข้าใช้ล่าสุด',
                        'format' => 'datetime',
                        'value' => function($model) {
                            return $model->last_login_at ?: 'ยังไม่เคยเข้าใช้';
                        },
                        'headerOptions' => ['style' => 'width: 150px'],
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'จัดการ',
                        'template' => '{view} {update} {reset-password} {delete}',
                        'headerOptions' => ['style' => 'width: 160px'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a(
                                    '<i class="fas fa-eye"></i>',
                                    ['view', 'id' => $model->id],
                                    [
                                        'title' => 'ดูรายละเอียด',
                                        'class' => 'btn btn-info btn-sm mr-1',
                                        'data-toggle' => 'tooltip',
                                    ]
                                );
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a(
                                    '<i class="fas fa-edit"></i>',
                                    ['update', 'id' => $model->id],
                                    [
                                        'title' => 'แก้ไข',
                                        'class' => 'btn btn-warning btn-sm mr-1',
                                        'data-toggle' => 'tooltip',
                                    ]
                                );
                            },
                            'reset-password' => function ($url, $model, $key) {
                                return Html::a(
                                    '<i class="fas fa-key"></i>',
                                    ['reset-password', 'id' => $model->id],
                                    [
                                        'title' => 'รีเซ็ตรหัสผ่าน',
                                        'class' => 'btn btn-secondary btn-sm mr-1',
                                        'data-toggle' => 'tooltip',
                                        'data-confirm' => 'คุณต้องการรีเซ็ตรหัสผ่านหรือไม่?',
                                    ]
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                // ป้องกันไม่ให้ลบตัวเอง
                                if ($model->id == Yii::$app->user->id) {
                                    return '';
                                }
                                return Html::a(
                                    '<i class="fas fa-trash"></i>',
                                    ['delete', 'id' => $model->id],
                                    [
                                        'title' => 'ลบ',
                                        'class' => 'btn btn-danger btn-sm',
                                        'data-toggle' => 'tooltip',
                                        'data-confirm' => 'คุณต้องการลบผู้ใช้นี้หรือไม่?',
                                        'data-method' => 'post',
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

</div>

<?php
// เพิ่ม JavaScript สำหรับ tooltip
$this->registerJs("
    $('[data-toggle=\"tooltip\"]').tooltip();
");
?>
