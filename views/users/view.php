<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->display_name ?: $model->username;
$this->params['breadcrumbs'][] = '<i class="fas fa-cog"></i> ระบบ';
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-users"></i> จัดการผู้ใช้งาน', 
    'url' => ['index'],
    'encode' => false,
];
$this->params['breadcrumbs'][] = '<i class="fas fa-user"></i> ' . $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>
                        ข้อมูลผู้ใช้: <?= Html::encode($this->title) ?>
                    </h3>
                    <div class="card-tools">
                        <?= Html::a('<i class="fas fa-list mr-1"></i> กลับไปรายการ', ['index'], ['class' => 'btn btn-default btn-sm']) ?>
                    </div>
                </div>
                <!-- /.card-header -->
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <?= Html::a('<i class="fas fa-edit mr-1"></i> แก้ไข', ['update', 'id' => $model->id], ['class' => 'btn btn-primary mr-2']) ?>
                            
                            <?= Html::a('<i class="fas fa-key mr-1"></i> รีเซ็ตรหัสผ่าน', ['reset-password', 'id' => $model->id], [
                                'class' => 'btn btn-warning mr-2',
                                'data' => [
                                    'confirm' => 'คุณต้องการรีเซ็ตรหัสผ่านหรือไม่?',
                                ],
                            ]) ?>
                            
                            <?php if ($model->id != Yii::$app->user->id): ?>
                            <?= Html::a('<i class="fas fa-trash mr-1"></i> ลบ', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'คุณต้องการลบผู้ใช้นี้หรือไม่?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped table-bordered detail-view'],
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'username',
                                'format' => 'raw',
                                'value' => Html::tag('span', Html::encode($model->username), [
                                    'class' => 'badge badge-info'
                                ]),
                            ],
                            'display_name',
                            'phone',
                            'email:email',
                            [
                                'attribute' => 'role',
                                'format' => 'raw',
                                'value' => function($model) {
                                    $class = $model->role === \app\models\User::ROLE_ADMIN ? 'badge-danger' : 'badge-primary';
                                    return Html::tag('span', $model->getRoleName(), [
                                        'class' => "badge {$class}"
                                    ]);
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function($model) {
                                    $class = $model->status === \app\models\User::STATUS_ACTIVE ? 'badge-success' : 'badge-secondary';
                                    $icon = $model->status === \app\models\User::STATUS_ACTIVE ? 'fa-check' : 'fa-times';
                                    return Html::tag('span', 
                                        '<i class="fas ' . $icon . ' mr-1"></i>' . $model->getStatusName(), 
                                        ['class' => "badge {$class}"]
                                    );
                                },
                            ],
                            'device_id',
                            [
                                'attribute' => 'last_login_at',
                                'format' => 'datetime',
                                'value' => $model->last_login_at ?: 'ยังไม่เคยเข้าใช้',
                            ],
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>

</div>
