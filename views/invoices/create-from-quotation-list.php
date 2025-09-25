<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Quotation;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */

$this->title = 'เลือกใบเสนอราคา';
$this->params['breadcrumbs'][] = ['label' => 'ใบแจ้งหนี้', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Create data provider for quotations
$dataProvider = new ActiveDataProvider([
    'query' => Quotation::find()
        ->with(['project', 'customer'])
        ->where(['status' => 1]) // Only active quotations
        ->andWhere(['not exists', 
            'SELECT 1 FROM {{%invoices}} i WHERE i.quotation_id = {{%quotations}}.id'
        ]), // Only quotations without invoices
    'sort' => [
        'defaultOrder' => ['id' => SORT_DESC]
    ],
]);
?>
<div class="quotation-select">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> <?= Html::encode($this->title) ?>
            </h3>
            <div class="card-tools">
                <?= Html::a('<i class="fas fa-arrow-left"></i> กลับ', ['index'], ['class' => 'btn btn-secondary']) ?>
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
                            return Html::a($model->code, ['quotations/view', 'id' => $model->id], [
                                'class' => 'text-primary',
                                'target' => '_blank'
                            ]);
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'format' => 'date',
                    ],
                    [
                        'attribute' => 'expire_date',
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
                    'subject',
                    [
                        'attribute' => 'grand_total',
                        'format' => ['decimal', 2],
                        'contentOptions' => ['class' => 'text-right'],
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{select}',
                        'buttons' => [
                            'select' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-check"></i> เลือก', ['create-from-quotation', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-success',
                                    'title' => 'สร้างใบแจ้งหนี้จากใบเสนอราคานี้'
                                ]);
                            },
                        ],
                        'contentOptions' => ['style' => 'width: 100px; text-align: center;'],
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
