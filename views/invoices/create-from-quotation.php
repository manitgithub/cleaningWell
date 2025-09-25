<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Invoice */
/* @var $quotation app\models\Quotation */

$this->title = 'สร้างใบแจ้งหนี้จาก: ' . $quotation->code;
$this->params['breadcrumbs'][] = ['label' => 'ใบแจ้งหนี้', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create-from-quotation">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-invoice"></i> <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-eye"></i> ดูใบเสนอราคา', ['quotations/view', 'id' => $quotation->id], ['class' => 'btn btn-info']) ?>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>หมายเหตุ:</strong> ข้อมูลทั้งหมดจะถูกคัดลอกมาจากใบเสนอราคา <?= $quotation->code ?> 
                คุณสามารถแก้ไขข้อมูลได้ก่อนบันทึก
            </div>
            
            <?= $this->render('_form', [
                'model' => $model,
                'fromQuotation' => true
            ]) ?>
        </div>
    </div>

</div>

<?php
$this->registerJs("
    // Auto-fill invoice data from quotation
    var quotationData = " . json_encode([
        'code' => $quotation->code,
        'date' => $quotation->date,
        'project_id' => $quotation->project_id,
        'customer_id' => $quotation->customer_id,
        'subject' => $quotation->subject,
        'notes' => $quotation->notes,
        'sub_total' => $quotation->sub_total,
        'discount_total' => $quotation->discount_total,
        'vat_rate' => $quotation->vat_rate,
        'vat_amount' => $quotation->vat_amount,
        'wht_rate' => $quotation->wht_rate,
        'wht_amount' => $quotation->wht_amount,
        'grand_total' => $quotation->grand_total,
        'items' => array_map(function($item) {
            return [
                'description' => $item->description,
                'qty' => $item->qty,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'line_discount' => $item->line_discount,
                'line_total' => $item->line_total
            ];
        }, $quotation->items)
    ]) . ";
    
    // Fill form with quotation data
    if (quotationData) {
        $('#invoice-subject').val(quotationData.subject);
        $('#invoice-notes').val(quotationData.notes);
        
        // Set due date to 30 days from invoice date
        var dueDate = new Date($('#invoice-date').val());
        dueDate.setDate(dueDate.getDate() + 30);
        $('#invoice-due_date').val(dueDate.toISOString().split('T')[0]);
        
        // Fill items
        invoiceItems = quotationData.items || [];
        renderItems();
        calculateTotals();
    }
", yii\web\View::POS_READY);
?>
