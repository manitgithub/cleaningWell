<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ใบแจ้งหนี้';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-invoice"></i> <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-plus"></i> สร้างใบแจ้งหนี้', ['create'], ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-file-invoice-dollar"></i> สร้างจากใบเสนอราคา', ['create-from-quotation-list'], ['class' => 'btn btn-info']) ?>
            </div>
        </div>
        <div class="card-body">
            <?php Pjax::begin(); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'code',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::a($model->code, ['view', 'id' => $model->id], ['class' => 'text-primary']);
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'format' => 'date',
                    ],
                    [
                        'attribute' => 'due_date',
                        'format' => 'date',
                    ],
                    [
                        'attribute' => 'project_id',
                        'value' => function($model) {
                            return $model->project ? $model->project->name : '-';
                        }
                    ],
                    [
                        'attribute' => 'customer_id',
                        'value' => function($model) {
                            return $model->customer ? $model->customer->name : '-';
                        }
                    ],
                    [
                        'attribute' => 'grand_total',
                        'format' => ['decimal', 2],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'balance',
                        'format' => ['decimal', 2],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->getStatusBadge();
                        },
                        'contentOptions' => ['class' => 'text-center'],
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {update-status} {delete}',
                        'buttons' => [
                            'update-status' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-edit"></i>', '#', [
                                    'title' => 'เปลี่ยนสถานะ',
                                    'class' => 'btn btn-sm btn-outline-warning',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#status-modal',
                                    'onclick' => "updateInvoiceStatus({$model->id}, {$model->status})"
                                ]);
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-eye"></i>', $url, [
                                    'title' => 'ดู',
                                    'class' => 'btn btn-sm btn-outline-info'
                                ]);
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-edit"></i>', $url, [
                                    'title' => 'แก้ไข',
                                    'class' => 'btn btn-sm btn-outline-primary'
                                ]);
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-trash"></i>', $url, [
                                    'title' => 'ลบ',
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'data-method' => 'post',
                                    'data-confirm' => 'คุณต้องการลบรายการนี้หรือไม่?',
                                ]);
                            },
                        ],
                        'contentOptions' => ['style' => 'width: 200px; text-align: center;'],
                    ],
                ],
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination justify-content-center'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>

</div>

<!-- Status Update Modal -->
<div class="modal fade" id="status-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เปลี่ยนสถานะใบแจ้งหนี้</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="status-form">
                    <div class="form-group">
                        <label>สถานะใหม่:</label>
                        <select class="form-control" id="new-status">
                            <option value="0">ร่าง</option>
                            <option value="1">ส่งแล้ว</option>
                            <option value="2">ชำระบางส่วน</option>
                            <option value="3">ชำระครบ</option>
                            <option value="4">เกินกำหนด</option>
                            <option value="5">ยกเลิก</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedInvoiceId = null;

function updateInvoiceStatus(invoiceId, currentStatus) {
    selectedInvoiceId = invoiceId;
    document.getElementById('new-status').value = currentStatus;
}

function submitStatusUpdate() {
    const newStatus = document.getElementById('new-status').value;
    if (selectedInvoiceId && newStatus !== null) {
        window.location.href = '<?= \yii\helpers\Url::to(['update-status']) ?>?id=' + selectedInvoiceId + '&status=' + newStatus;
    }
}
</script>
