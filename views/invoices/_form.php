<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use app\models\Project;
use app\models\Customer;

/* @var $this yii\web\View */
/* @var $model app\models\Invoice */
/* @var $form yii\widgets\ActiveForm */
/* @var $fromQuotation boolean */

$fromQuotation = isset($fromQuotation) ? $fromQuotation : false;
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin([
        'id' => 'invoice-form',
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'readonly' => true]) ?>
            
            <?= $form->field($model, 'date')->textInput(['type' => 'date']) ?>
            
            <?= $form->field($model, 'due_date')->textInput(['type' => 'date']) ?>
        </div>
        
        <div class="col-md-6">
            <?= $form->field($model, 'project_id')->dropDownList(
                ArrayHelper::map(Project::find()->where(['status' => 'active'])->all(), 'id', 'name'),
                ['prompt' => 'เลือกโครงการ']
            ) ?>

            <?= $form->field($model, 'customer_id')->dropDownList(
                ArrayHelper::map(Customer::find()->where(['status' => 'active'])->all(), 'id', 'name'),
                ['prompt' => 'เลือกลูกค้า']
            ) ?>

            <?= $form->field($model, 'status')->dropDownList([
                0 => 'แบบร่าง',
                1 => 'ส่งแล้ว',
                2 => 'ชำระบางส่วน',
                3 => 'ชำระครบ',
                4 => 'เกินกำหนด',
                5 => 'ยกเลิก',
            ], ['prompt' => 'เลือกสถานะ']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
        </div>
    </div>

    <!-- รายการสินค้า/บริการ -->
    <div class="card mt-3">
        <div class="card-header">
            <h4><i class="fas fa-list"></i> รายการสินค้า/บริการ</h4>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-success mb-3" onclick="addItem()">
                <i class="fas fa-plus"></i> เพิ่มรายการ
            </button>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="items-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="35%">รายละเอียด</th>
                            <th width="10%">จำนวน</th>
                            <th width="10%">หน่วย</th>
                            <th width="15%">ราคาต่อหน่วย</th>
                            <th width="10%">ส่วนลด</th>
                            <th width="15%">รวม</th>
                            <th width="5%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        <!-- Items จะถูกเพิ่มที่นี่ผ่าน JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- สรุปยอดเงิน -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <table class="table">
                        <tr>
                            <td><strong>รวมก่อนลด:</strong></td>
                            <td class="text-right">
                                <?= $form->field($model, 'sub_total')->textInput(['readonly' => true, 'class' => 'form-control text-right'])->label(false) ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>ส่วนลดรวม:</strong></td>
                            <td class="text-right">
                                <?= $form->field($model, 'discount_total')->textInput(['readonly' => true, 'class' => 'form-control text-right'])->label(false) ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>VAT (%):</strong></td>
                            <td class="text-right">
                                <div class="input-group">
                                    <?= $form->field($model, 'vat_rate')->textInput(['class' => 'form-control text-right', 'onchange' => 'calculateTotals()'])->label(false) ?>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>VAT:</strong></td>
                            <td class="text-right">
                                <?= $form->field($model, 'vat_amount')->textInput(['readonly' => true, 'class' => 'form-control text-right'])->label(false) ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>WHT (%):</strong></td>
                            <td class="text-right">
                                <div class="input-group">
                                    <?= $form->field($model, 'wht_rate')->textInput(['class' => 'form-control text-right', 'onchange' => 'calculateTotals()'])->label(false) ?>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>WHT:</strong></td>
                            <td class="text-right">
                                <?= $form->field($model, 'wht_amount')->textInput(['readonly' => true, 'class' => 'form-control text-right'])->label(false) ?>
                            </td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong>รวมสุทธิ:</strong></td>
                            <td class="text-right">
                                <?= $form->field($model, 'grand_total')->textInput(['readonly' => true, 'class' => 'form-control text-right font-weight-bold'])->label(false) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('<i class="fas fa-save"></i> บันทึก', ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fas fa-times"></i> ยกเลิก', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// Register invoice-form.js
$this->registerJsFile('@web/js/invoice-form.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
