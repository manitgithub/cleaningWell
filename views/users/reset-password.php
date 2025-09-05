<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'รีเซ็ตรหัสผ่าน: ' . $model->username;
$this->params['breadcrumbs'][] = '<i class="fas fa-cog"></i> ระบบ';
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-users"></i> จัดการผู้ใช้งาน', 
    'url' => ['index'],
    'encode' => false,
];
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-user"></i> ' . ($model->display_name ?: $model->username), 
    'url' => ['view', 'id' => $model->id],
    'encode' => false,
];
$this->params['breadcrumbs'][] = '<i class="fas fa-key"></i> รีเซ็ตรหัสผ่าน';
?>
<div class="user-reset-password">

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-2"></i>
                        รีเซ็ตรหัสผ่าน
                    </h3>
                </div>
                <!-- /.card-header -->
                
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> คำเตือน!</h5>
                        การรีเซ็ตรหัสผ่านจะเปลี่ยนรหัสผ่านเก่าทันที ผู้ใช้จะต้องใช้รหัสผ่านใหม่ในการเข้าสู่ระบบครั้งต่อไป
                    </div>

                    <div class="user-info mb-3">
                        <strong>ผู้ใช้:</strong> <?= Html::encode($model->display_name ?: $model->username) ?><br>
                        <strong>ชื่อผู้ใช้:</strong> <?= Html::encode($model->username) ?>
                    </div>

                    <?php $form = ActiveForm::begin(); ?>

                    <div class="form-group">
                        <label class="control-label">รหัสผ่านใหม่</label>
                        <?= Html::passwordInput('password', '', [
                            'class' => 'form-control',
                            'placeholder' => 'กรุณาใส่รหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร)',
                            'required' => true,
                            'minlength' => 6,
                            'id' => 'new-password'
                        ]) ?>
                        <small class="form-text text-muted">รหัสผ่านควรมีความยาวอย่างน้อย 6 ตัวอักษร</small>
                    </div>

                    <div class="form-group">
                        <label class="control-label">ยืนยันรหัสผ่านใหม่</label>
                        <?= Html::passwordInput('password_confirm', '', [
                            'class' => 'form-control',
                            'placeholder' => 'ยืนยันรหัสผ่านใหม่',
                            'required' => true,
                            'id' => 'confirm-password'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
                <!-- /.card-body -->
                
                <div class="card-footer">
                    <div class="form-group mb-0">
                        <?= Html::submitButton('<i class="fas fa-key mr-1"></i> รีเซ็ตรหัสผ่าน', [
                            'class' => 'btn btn-warning',
                            'id' => 'reset-btn'
                        ]) ?>
                        <?= Html::a('<i class="fas fa-arrow-left mr-1"></i> ยกเลิก', ['view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->
        </div>
    </div>

</div>

<?php
// เพิ่ม JavaScript สำหรับตรวจสอบรหัสผ่าน
$this->registerJs("
    $('#reset-btn').click(function(e) {
        e.preventDefault();
        
        var password = $('#new-password').val();
        var confirmPassword = $('#confirm-password').val();
        
        if (password.length < 6) {
            alert('รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร');
            return false;
        }
        
        if (password !== confirmPassword) {
            alert('รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน');
            return false;
        }
        
        if (confirm('คุณต้องการรีเซ็ตรหัสผ่านหรือไม่?')) {
            // สร้าง form และส่งข้อมูล
            var form = $('<form>', {
                'method': 'POST',
                'action': window.location.href
            });
            
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'password',
                'value': password
            }));
            
            // เพิ่ม CSRF token
            form.append($('input[name=\"_csrf\"]').clone());
            
            $('body').append(form);
            form.submit();
        }
    });
");
?>
