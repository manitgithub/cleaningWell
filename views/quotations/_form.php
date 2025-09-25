<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Quotation */
/* @var $form yii\widgets\ActiveForm */
/* @var $projects array */
/* @var $customers array */
/* @var $items array */

// Register JS for dynamic items
$this->registerJsFile('@web/js/quotation-form.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="quotation-form">

    <?php $form = ActiveForm::begin(['id' => 'quotation-form']); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'readonly' => true]) ?>
            <?= $form->field($model, 'date')->input('date') ?>
            <?= $form->field($model, 'expire_date')->input('date') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'project_id')->dropDownList(
                ArrayHelper::map($projects, 'id', 'name'),
                ['prompt' => 'เลือกโครงการ', 'id' => 'project-select']
            ) ?>
            <?= $form->field($model, 'customer_id')->dropDownList(
                ArrayHelper::map($customers, 'id', 'name'),
                ['prompt' => 'เลือกลูกค้า', 'id' => 'customer-select']
            ) ?>
            <?= $form->field($model, 'status')->dropDownList([
                1 => 'ใช้งาน',
                0 => 'ปิดใช้งาน'
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
        </div>
    </div>

    <hr>

    <!-- Dynamic Items Section -->
    <div class="form-group">
        <label><i class="fas fa-list"></i> รายการสินค้า/บริการ</label>
        <div class="table-responsive">
            <table class="table table-bordered" id="items-table">
                <thead class="thead-light">
                    <tr>
                        <th width="25%">สินค้า/บริการ</th>
                        <th width="30%">รายละเอียด</th>
                        <th width="10%">จำนวน</th>
                        <th width="12%">ราคาต่อหน่วย</th>
                        <th width="12%">ส่วนลด</th>
                        <th width="8%">รวม</th>
                        <th width="3%"></th>
                    </tr>
                </thead>
                <tbody id="items-tbody">
                    <?php if (!$model->isNewRecord && $model->items): ?>
                        <?php foreach ($model->items as $index => $item): ?>
                        <tr class="item-row">
                            <td>
                                <select class="form-control item-select" name="QuotationItem[<?= $index ?>][item_id]">
                                    <option value="">เลือกสินค้า</option>
                                    <?php foreach ($items as $itemOption): ?>
                                        <option value="<?= $itemOption->id ?>" <?= $item->item_id == $itemOption->id ? 'selected' : '' ?>>
                                            <?= Html::encode($itemOption->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control item-description" name="QuotationItem[<?= $index ?>][description]" value="<?= Html::encode($item->description) ?>" required>
                            </td>
                            <td>
                                <input type="number" class="form-control item-qty" name="QuotationItem[<?= $index ?>][qty]" value="<?= $item->qty ?>" step="0.01" min="0" required>
                            </td>
                            <td>
                                <input type="number" class="form-control item-price" name="QuotationItem[<?= $index ?>][unit_price]" value="<?= $item->unit_price ?>" step="0.01" min="0" required>
                            </td>
                            <td>
                                <input type="number" class="form-control item-discount" name="QuotationItem[<?= $index ?>][line_discount]" value="<?= $item->line_discount ?>" step="0.01" min="0">
                            </td>
                            <td>
                                <input type="number" class="form-control item-total" name="QuotationItem[<?= $index ?>][line_total]" value="<?= $item->line_total ?>" step="0.01" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="item-row">
                            <td>
                                <select class="form-control item-select" name="QuotationItem[0][item_id]">
                                    <option value="">เลือกสินค้า</option>
                                    <?php foreach ($items as $item): ?>
                                        <option value="<?= $item->id ?>"><?= Html::encode($item->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control item-description" name="QuotationItem[0][description]" required>
                            </td>
                            <td>
                                <input type="number" class="form-control item-qty" name="QuotationItem[0][qty]" step="0.01" min="0" required>
                            </td>
                            <td>
                                <input type="number" class="form-control item-price" name="QuotationItem[0][unit_price]" step="0.01" min="0" required>
                            </td>
                            <td>
                                <input type="number" class="form-control item-discount" name="QuotationItem[0][line_discount]" step="0.01" min="0" value="0">
                            </td>
                            <td>
                                <input type="number" class="form-control item-total" name="QuotationItem[0][line_total]" step="0.01" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-success" id="add-item">
            <i class="fas fa-plus"></i> เพิ่มรายการ
        </button>
    </div>

    <hr>

    <!-- Summary Section -->
    <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-4">
            <div class="form-group">
                <?= $form->field($model, 'sub_total')->textInput(['readonly' => true, 'class' => 'form-control text-right']) ?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'discount_total')->textInput(['step' => '0.01', 'class' => 'form-control text-right']) ?>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'vat_rate')->textInput(['step' => '0.01', 'class' => 'form-control text-right']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'vat_amount')->textInput(['readonly' => true, 'class' => 'form-control text-right']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'wht_rate')->textInput(['step' => '0.01', 'class' => 'form-control text-right']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'wht_amount')->textInput(['readonly' => true, 'class' => 'form-control text-right']) ?>
                </div>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'grand_total')->textInput(['readonly' => true, 'class' => 'form-control text-right font-weight-bold']) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> บันทึก', ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fas fa-times"></i> ยกเลิก', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- Hidden template for new item row -->
<template id="item-row-template">
    <tr class="item-row">
        <td>
            <select class="form-control item-select" name="QuotationItem[INDEX][item_id]">
                <option value="">เลือกสินค้า</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?= $item->id ?>"><?= Html::encode($item->name) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="text" class="form-control item-description" name="QuotationItem[INDEX][description]" required>
        </td>
        <td>
            <input type="number" class="form-control item-qty" name="QuotationItem[INDEX][qty]" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" class="form-control item-price" name="QuotationItem[INDEX][unit_price]" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" class="form-control item-discount" name="QuotationItem[INDEX][line_discount]" step="0.01" min="0" value="0">
        </td>
        <td>
            <input type="number" class="form-control item-total" name="QuotationItem[INDEX][line_total]" step="0.01" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-item">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>
