<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput([
                'maxlength' => true,
                'placeholder' => 'ชื่อสินค้า/บริการ',
                'class' => 'form-control'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'is_active')->dropDownList(
                \app\models\Item::getStatusOptions(),
                ['class' => 'form-control']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea([
                'rows' => 3,
                'placeholder' => 'รายละเอียดเพิ่มเติม',
                'class' => 'form-control'
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'unit')->dropDownList(
                \app\models\Item::getUnitOptions(),
                [
                    'class' => 'form-control'
                ]
            ) ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'unit_price')->textInput([
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'placeholder' => '0.00',
                'class' => 'form-control price-input'
            ]) ?>
        </div>

    </div>



    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> บันทึก', [
            'class' => 'btn btn-success'
        ]) ?>
        <?= Html::a('<i class="fas fa-times"></i> ยกเลิก', ['index'], [
            'class' => 'btn btn-secondary'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    // Auto format price input
    $('.price-input').on('blur', function() {
        let value = parseFloat(this.value);
        if (!isNaN(value)) {
            this.value = value.toFixed(2);
        }
    });

    // Validate WHT percentage
    $('#item-wht_default').on('blur', function() {
        let value = parseFloat(this.value);
        if (!isNaN(value)) {
            if (value > 100) {
                alert('อัตราหัก ณ ที่จ่ายไม่ควรเกิน 100%');
                this.value = '0.00';
                this.focus();
            } else {
                this.value = value.toFixed(2);
            }
        }
    });

    // Price validation
    $('#item-unit_price').on('blur', function() {
        let value = parseFloat(this.value);
        if (isNaN(value) || value < 0) {
            alert('ราคาต้องเป็นตัวเลขและมากกว่าหรือเท่ากับ 0');
            this.value = '0.00';
            this.focus();
        }
    });
");
?>
