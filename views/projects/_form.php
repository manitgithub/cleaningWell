<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Project */
/* @var $form yii\widgets\ActiveForm */
/* @var $customers array */

?>

<div class="project-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'code')->textInput([
                'maxlength' => true,
                'placeholder' => 'รหัสโครงการ (เว้นว่างเพื่อสร้างอัตโนมัติ)',
                'class' => 'form-control'
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList(
                \app\models\Project::getStatusOptions(),
                ['class' => 'form-control']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'name')->textInput([
                'maxlength' => true,
                'placeholder' => 'ชื่อโครงการ',
                'class' => 'form-control'
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'customer_id')->dropDownList(
                ArrayHelper::map($customers, 'id', 'name'),
                [
                    'prompt' => '-- เลือกลูกค้า --',
                    'class' => 'form-control'
                ]
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'start_date')->input('date', [
                'class' => 'form-control'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'end_date')->input('date', [
                'class' => 'form-control'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'budget')->textInput([
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'placeholder' => '0.00',
                'class' => 'form-control budget-input'
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'notes')->textarea([
                'rows' => 4,
                'placeholder' => 'หมายเหตุ (ถ้ามี)',
                'class' => 'form-control'
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
    // Auto format budget input
    $('.budget-input').on('blur', function() {
        let value = parseFloat(this.value);
        if (!isNaN(value)) {
            this.value = value.toFixed(2);
        }
    });

    // Validate end date
    $('#project-start_date, #project-end_date').on('change', function() {
        let startDate = $('#project-start_date').val();
        let endDate = $('#project-end_date').val();
        
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            alert('วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่ม');
            $('#project-end_date').val('');
        }
    });
");
?>
