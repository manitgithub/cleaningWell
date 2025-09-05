<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Customer;
use app\models\CustomerType;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => '{label}{input}{error}',
            'labelOptions' => ['class' => 'form-label'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ข้อมูลพื้นฐาน</h3>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'ชื่อลูกค้า/บริษัท',
                        'required' => true,
                    ]) ?>

                    <?= $form->field($model, 'customer_type_id')->dropDownList(
                        CustomerType::getTypeOptions(),
                        ['prompt' => 'เลือกประเภทลูกค้า']
                    ) ?>

                    <?= $form->field($model, 'branch')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'สาขา (ถ้ามี)',
                    ]) ?>

                    <?= $form->field($model, 'contact_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'ชื่อผู้ติดต่อ',
                    ]) ?>

                    <?= $form->field($model, 'status')->dropDownList(
                        Customer::getStatusOptions(),
                        ['prompt' => 'เลือกสถานะ']
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">ข้อมูลติดต่อ</h3>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'phone')->textInput([
                        'maxlength' => true,
                        'placeholder' => '02-xxx-xxxx หรือ 08x-xxx-xxxx',
                    ]) ?>

                    <?= $form->field($model, 'email')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'email@domain.com',
                        'type' => 'email',
                    ]) ?>

                    <?= $form->field($model, 'tax_id')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'เลขประจำตัวผู้เสียภาษี 13 หลัก',
                    ]) ?>

                    <?= $form->field($model, 'citizen_id')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'เลขบัตรประชาชน 13 หลัก',
                    ]) ?>

                    <?= $form->field($model, 'address')->textarea([
                        'rows' => 4,
                        'placeholder' => 'ที่อยู่ของลูกค้า',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">การจัดการ</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? '<i class="fas fa-save"></i> บันทึก' : '<i class="fas fa-save"></i> อัปเดต', [
                            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                            'encode' => false,
                        ]) ?>
                        <?= Html::a('<i class="fas fa-times"></i> ยกเลิก', ['index'], [
                            'class' => 'btn btn-secondary ml-2',
                            'encode' => false,
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    // Auto format phone number
    $('#customer-phone').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length >= 10) {
            if (value.startsWith('02')) {
                $(this).val(value.replace(/(\d{2})(\d{3})(\d{4})/, '$1-$2-$3'));
            } else if (value.startsWith('0')) {
                $(this).val(value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3'));
            }
        }
    });
    
    // Auto format tax ID
    $('#customer-tax_id').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length >= 13) {
            $(this).val(value.substring(0, 13).replace(/(\d{1})(\d{4})(\d{5})(\d{2})(\d{1})/, '$1-$2-$3-$4-$5'));
        }
    });
    
    // Auto format citizen ID
    $('#customer-citizen_id').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length >= 13) {
            $(this).val(value.substring(0, 13).replace(/(\d{1})(\d{4})(\d{5})(\d{2})(\d{1})/, '$1-$2-$3-$4-$5'));
        }
    });
");
?>
