<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-edit mr-2"></i>
                <?= $model->isNewRecord ? 'เพิ่มผู้ใช้ใหม่' : 'แก้ไขข้อมูลผู้ใช้' ?>
            </h3>
        </div>
        <!-- /.card-header -->
        
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'control-label'],
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'username')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'ชื่อผู้ใช้สำหรับเข้าสู่ระบบ'
                    ]) ?>

                    <?php if ($model->isNewRecord): ?>
                        <?= $form->field($model, 'password')->passwordInput([
                            'maxlength' => true,
                            'placeholder' => 'รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)'
                        ]) ?>
                    <?php endif; ?>

                    <?= $form->field($model, 'display_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'ชื่อที่แสดงในระบบ'
                    ]) ?>
                </div>
                
                <div class="col-md-6">
                    <?= $form->field($model, 'phone')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'เบอร์โทรศัพท์'
                    ]) ?>

                    <?= $form->field($model, 'email')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'อีเมล'
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'role')->dropDownList([
                        User::ROLE_ADMIN => 'ผู้ดูแลระบบ',
                        User::ROLE_HOUSEKEEPER => 'แม่บ้าน',
                    ], [
                        'prompt' => 'เลือกบทบาท',
                        'class' => 'form-control'
                    ]) ?>
                </div>
                
                <div class="col-md-6">
                    <?= $form->field($model, 'status')->dropDownList([
                        User::STATUS_ACTIVE => 'ใช้งาน',
                        User::STATUS_INACTIVE => 'ไม่ใช้งาน',
                    ], [
                        'prompt' => 'เลือกสถานะ',
                        'class' => 'form-control'
                    ]) ?>
                </div>
            </div>

            <?php if (!$model->isNewRecord): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>หมายเหตุ:</strong> หากต้องการเปลี่ยนรหัสผ่าน กรุณาใช้ฟังก์ชัน "รีเซ็ตรหัสผ่าน"
            </div>
            <?php endif; ?>

        </div>
        <!-- /.card-body -->
        
        <div class="card-footer">
            <div class="form-group mb-0">
                <?= Html::submitButton(
                    $model->isNewRecord ? '<i class="fas fa-save mr-1"></i> สร้าง' : '<i class="fas fa-save mr-1"></i> บันทึก', 
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
                ) ?>
                <?= Html::a('<i class="fas fa-times mr-1"></i> ยกเลิก', ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
        <!-- /.card-footer -->
    </div>
    <!-- /.card -->

    <?php ActiveForm::end(); ?>

</div>
