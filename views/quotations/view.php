<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Quotation */

$this->title = 'ใบเสนอราคา: ' . $model->code;
$this->params['breadcrumbs'][] = ['label' => 'ใบเสนอราคา', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quotation-view">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-edit"></i> แก้ไข', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fas fa-file-pdf"></i> Export PDF', ['export-pdf', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
                <?= Html::a('<i class="fas fa-file-invoice"></i> สร้างใบแจ้งหนี้', ['invoices/create-from-quotation', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-trash"></i> ลบ', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'คุณต้องการลบรายการนี้หรือไม่?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'code',
                            'date:date',
                            'expire_date:date',
                            [
                                'attribute' => 'project_id',
                                'value' => $model->project ? $model->project->name : '-',
                            ],
                            [
                                'attribute' => 'customer_id',
                                'value' => $model->customer ? $model->customer->name : '-',
                            ],
                        ],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'subject',
                            'notes:ntext',
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => $model->status == 1 ? '<span class="badge badge-success">ใช้งาน</span>' : '<span class="badge badge-secondary">ปิดใช้งาน</span>',
                            ],
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h4><i class="fas fa-list"></i> รายการสินค้า/บริการ</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="40%">รายละเอียด</th>
                            <th width="10%" class="text-center">จำนวน</th>
                            <th width="15%" class="text-right">ราคาต่อหน่วย</th>
                            <th width="15%" class="text-right">ส่วนลด</th>
                            <th width="15%" class="text-right">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($model->items as $item): ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td><?= Html::encode($item->description) ?></td>
                            <td class="text-center"><?= number_format($item->qty, 2) ?></td>
                            <td class="text-right"><?= number_format($item->unit_price, 2) ?></td>
                            <td class="text-right"><?= number_format($item->line_discount, 2) ?></td>
                            <td class="text-right"><?= number_format($item->line_total, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>รวมก่อนลด:</strong></td>
                            <td class="text-right"><strong><?= number_format($model->sub_total, 2) ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>ส่วนลดรวม:</strong></td>
                            <td class="text-right"><strong><?= number_format($model->discount_total, 2) ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>VAT (<?= $model->vat_rate ?>%):</strong></td>
                            <td class="text-right"><strong><?= number_format($model->vat_amount, 2) ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right"><strong>WHT (<?= $model->wht_rate ?>%):</strong></td>
                            <td class="text-right"><strong><?= number_format($model->wht_amount, 2) ?></strong></td>
                        </tr>
                        <tr class="table-primary">
                            <td colspan="5" class="text-right"><strong>รวมสุทธิ:</strong></td>
                            <td class="text-right"><strong><?= number_format($model->grand_total, 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
