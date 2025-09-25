<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Quotation */
/* @var $projects array */
/* @var $customers array */
/* @var $items array */

$this->title = 'สร้างใบเสนอราคา';
$this->params['breadcrumbs'][] = ['label' => 'ใบเสนอราคา', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-create">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-plus"></i> <?= Html::encode($this->title) ?>
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
