<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $settings array */

$this->title = 'ตั้งค่าระบบ';
$this->params['breadcrumbs'][] = '<i class="fas fa-cog"></i> ระบบ';
$this->params['breadcrumbs'][] = '<i class="fas fa-sliders-h"></i> ' . $this->title;
?>
<div class="settings-index">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-sliders-h"></i> <?= Html::encode($this->title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <?= Html::a('<i class="fas fa-undo"></i> เริ่มต้นค่าเริ่มต้น', 
                            ['init-defaults'], 
                            [
                                'class' => 'btn btn-warning',
                                'data-confirm' => 'คุณต้องการเริ่มต้นค่าเริ่มต้นหรือไม่?',
                                'encode' => false
                            ]
                        ) ?>
                        <?= Html::a('<i class="fas fa-sync"></i> รีเซ็ตตัวนับเอกสาร', 
                            ['reset-counters'], 
                            [
                                'class' => 'btn btn-danger ml-2',
                                'data-confirm' => 'คุณต้องการรีเซ็ตตัวนับเอกสารทั้งหมดหรือไม่?',
                                'encode' => false
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => [
                    'template' => '{label}{input}{error}',
                    'labelOptions' => ['class' => 'form-label'],
                ],
            ]); ?>

            <div class="row">
                <!-- VAT & WHT Settings -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-percent"></i> ภาษี VAT & WHT</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>เปิดใช้งาน VAT</label>
                                        <?= Html::checkbox('vat_enabled', $settings['vat_enabled'], [
                                            'class' => 'form-check-input',
                                            'id' => 'vat_enabled'
                                        ]) ?>
                                        <label class="form-check-label ml-2" for="vat_enabled">เปิดใช้งาน</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>อัตรา VAT (%)</label>
                                        <?= Html::textInput('vat_rate', $settings['vat_rate'], [
                                            'class' => 'form-control',
                                            'placeholder' => '7'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>เปิดใช้งาน WHT</label>
                                        <?= Html::checkbox('wht_enabled', $settings['wht_enabled'], [
                                            'class' => 'form-check-input',
                                            'id' => 'wht_enabled'
                                        ]) ?>
                                        <label class="form-check-label ml-2" for="wht_enabled">เปิดใช้งาน</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>อัตรา WHT (%)</label>
                                        <?= Html::textInput('wht_rate', $settings['wht_rate'], [
                                            'class' => 'form-control',
                                            'placeholder' => '3'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-building"></i> ข้อมูลบริษัท</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>ชื่อบริษัท</label>
                                <?= Html::textInput('company_name', $settings['company_name'], [
                                    'class' => 'form-control',
                                    'placeholder' => 'CleaningWell Co., Ltd.'
                                ]) ?>
                            </div>
                            <div class="form-group">
                                <label>เลขประจำตัวผู้เสียภาษี</label>
                                <?= Html::textInput('company_tax_id', $settings['company_tax_id'], [
                                    'class' => 'form-control',
                                    'placeholder' => '1234567890123'
                                ]) ?>
                            </div>
                            <div class="form-group">
                                <label>ที่อยู่</label>
                                <?= Html::textarea('company_address', $settings['company_address'], [
                                    'class' => 'form-control',
                                    'rows' => 3,
                                    'placeholder' => 'ที่อยู่บริษัท'
                                ]) ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>โทรศัพท์</label>
                                        <?= Html::textInput('company_phone', $settings['company_phone'], [
                                            'class' => 'form-control',
                                            'placeholder' => '02-xxx-xxxx'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>อีเมล</label>
                                        <?= Html::textInput('company_email', $settings['company_email'], [
                                            'class' => 'form-control',
                                            'placeholder' => 'info@company.com'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>โลโก้บริษัท</label>
                                <?= Html::fileInput('company_logo', '', [
                                    'class' => 'form-control-file',
                                    'accept' => 'image/*'
                                ]) ?>
                                <?php if ($settings['company_logo']): ?>
                                    <div class="mt-2">
                                        <img src="<?= Html::encode($settings['company_logo']) ?>" alt="Logo" style="max-height: 50px;">
                                        <small class="text-muted d-block">โลโก้ปัจจุบัน</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Document Numbering -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-alt"></i> รูปแบบเลขที่เอกสาร</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Quotation -->
                                <div class="col-md-4">
                                    <h5>ใบเสนอราคา (Quotation)</h5>
                                    <div class="form-group">
                                        <label>คำนำหน้า</label>
                                        <?= Html::textInput('quotation_prefix', $settings['quotation_prefix'], [
                                            'class' => 'form-control',
                                            'placeholder' => 'QT'
                                        ]) ?>
                                    </div>
                                    <div class="form-group">
                                        <label>รูปแบบ</label>
                                        <?= Html::textInput('quotation_format', $settings['quotation_format'], [
                                            'class' => 'form-control',
                                            'placeholder' => '{prefix}{year}{month}-{number:4}'
                                        ]) ?>
                                        <small class="text-muted">ตัวอย่าง: QT202509-0001</small>
                                    </div>
                                    <div class="form-group">
                                        <label>ตัวนับปัจจุบัน</label>
                                        <?= Html::textInput('quotation_counter', $settings['quotation_counter'], [
                                            'class' => 'form-control',
                                            'readonly' => true
                                        ]) ?>
                                    </div>
                                </div>

                                <!-- Invoice -->
                                <div class="col-md-4">
                                    <h5>ใบแจ้งหนี้ (Invoice)</h5>
                                    <div class="form-group">
                                        <label>คำนำหน้า</label>
                                        <?= Html::textInput('invoice_prefix', $settings['invoice_prefix'], [
                                            'class' => 'form-control',
                                            'placeholder' => 'INV'
                                        ]) ?>
                                    </div>
                                    <div class="form-group">
                                        <label>รูปแบบ</label>
                                        <?= Html::textInput('invoice_format', $settings['invoice_format'], [
                                            'class' => 'form-control',
                                            'placeholder' => '{prefix}{year}{month}-{number:4}'
                                        ]) ?>
                                        <small class="text-muted">ตัวอย่าง: INV202509-0001</small>
                                    </div>
                                    <div class="form-group">
                                        <label>ตัวนับปัจจุบัน</label>
                                        <?= Html::textInput('invoice_counter', $settings['invoice_counter'], [
                                            'class' => 'form-control',
                                            'readonly' => true
                                        ]) ?>
                                    </div>
                                </div>

                                <!-- Receipt -->
                                <div class="col-md-4">
                                    <h5>ใบเสร็จรับเงิน (Receipt)</h5>
                                    <div class="form-group">
                                        <label>คำนำหน้า</label>
                                        <?= Html::textInput('receipt_prefix', $settings['receipt_prefix'], [
                                            'class' => 'form-control',
                                            'placeholder' => 'RC'
                                        ]) ?>
                                    </div>
                                    <div class="form-group">
                                        <label>รูปแบบ</label>
                                        <?= Html::textInput('receipt_format', $settings['receipt_format'], [
                                            'class' => 'form-control',
                                            'placeholder' => '{prefix}{year}{month}-{number:4}'
                                        ]) ?>
                                        <small class="text-muted">ตัวอย่าง: RC202509-0001</small>
                                    </div>
                                    <div class="form-group">
                                        <label>ตัวนับปัจจุบัน</label>
                                        <?= Html::textInput('receipt_counter', $settings['receipt_counter'], [
                                            'class' => 'form-control',
                                            'readonly' => true
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- GPS & Geofence Settings -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> GPS & Geofence</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>ความแม่นยำ GPS ที่ต้องการ (เมตร)</label>
                                <?= Html::textInput('gps_accuracy_required', $settings['gps_accuracy_required'], [
                                    'class' => 'form-control',
                                    'placeholder' => '50'
                                ]) ?>
                                <small class="text-muted">ค่าที่แนะนำ: 50 เมตร</small>
                            </div>
                            <div class="form-group">
                                <label>รัศมี Geofence เริ่มต้น (เมตร)</label>
                                <?= Html::textInput('geofence_radius_default', $settings['geofence_radius_default'], [
                                    'class' => 'form-control',
                                    'placeholder' => '100'
                                ]) ?>
                                <small class="text-muted">รัศมีสำหรับการเช็คอิน/เอาต์</small>
                            </div>
                            <div class="form-group">
                                <label>อนุญาตเช็คอินด้วยตนเอง</label>
                                <?= Html::checkbox('allow_manual_checkin', $settings['allow_manual_checkin'], [
                                    'class' => 'form-check-input',
                                    'id' => 'allow_manual_checkin'
                                ]) ?>
                                <label class="form-check-label ml-2" for="allow_manual_checkin">อนุญาต</label>
                                <small class="text-muted d-block">อนุญาตให้เช็คอินนอกพื้นที่ Geofence</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-save"></i> บันทึกการตั้งค่า</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                การเปลี่ยนแปลงการตั้งค่าจะมีผลทันทีหลังจากบันทึก 
                                กรุณาตรวจสอบความถูกต้องก่อนบันทึก
                            </p>
                            <div class="form-group">
                                <?= Html::submitButton('<i class="fas fa-save"></i> บันทึกการตั้งค่า', [
                                    'class' => 'btn btn-success btn-lg btn-block',
                                    'encode' => false
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    // Preview uploaded logo
    $('input[name=\"company_logo\"]').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = '<div class=\"mt-2\"><img src=\"' + e.target.result + '\" alt=\"Preview\" style=\"max-height: 50px;\"><small class=\"text-muted d-block\">ตัวอย่างโลโก้ใหม่</small></div>';
                $('input[name=\"company_logo\"]').parent().find('.mt-2').remove();
                $('input[name=\"company_logo\"]').parent().append(preview);
            };
            reader.readAsDataURL(file);
        }
    });
");
?>
