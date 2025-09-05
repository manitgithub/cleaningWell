<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Project */
/* @var $customers array */

$this->title = 'แก้ไขโครงการ: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>


<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>
                    แก้ไขโครงการ
                </h3>
                <div class="card-tools">
                    <?= Html::a('<i class="fas fa-eye"></i> ดูรายละเอียด', ['view', 'id' => $model->id], [
                        'class' => 'btn btn-info btn-sm'
                    ]) ?>
                    <?= Html::a('<i class="fas fa-list"></i> กลับไปรายการ', ['index'], [
                        'class' => 'btn btn-secondary btn-sm'
                    ]) ?>
                </div>
            </div>
            <div class="card-body">
                <?= $this->render('_form', [
                    'model' => $model,
                    'customers' => $customers,
                ]) ?>
            </div>
        </div>
    </div>
</section>
