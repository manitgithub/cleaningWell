<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Project */
/* @var $customers array */

$this->title = 'สร้างโครงการใหม่';
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus"></i>
                    สร้างโครงการใหม่
                </h3>
                <div class="card-tools">
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
