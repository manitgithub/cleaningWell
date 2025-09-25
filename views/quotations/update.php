<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quotation */
/* @var $projects array */
/* @var $customers array */
/* @var $items array */

$this->title = 'แก้ไขใบเสนอราคา: ' . $model->code;
$this->params['breadcrumbs'][] = ['label' => 'ใบเสนอราคา', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>
<div class="quotation-update">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit"></i> <?= Html::encode($this->title) ?>
            </h3>
        </div>
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
                'projects' => $projects,
                'customers' => $customers,
                'items' => $items,
            ]) ?>
        </div>
    </div>

</div>
