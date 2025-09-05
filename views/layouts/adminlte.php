<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AdminLteAsset;
use app\assets\FontAwesomeAsset;
use app\assets\CustomAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AdminLteAsset::register($this);
FontAwesomeAsset::register($this);
CustomAsset::register($this);

$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<?php $this->beginBody() ?>

<div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <div class="animation__shake">
            <i class="fas fa-broom fa-3x text-primary"></i>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <?= Html::a('หน้าแรก', ['/site/index'], ['class' => 'nav-link']) ?>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- User Menu -->
            <?php if (!Yii::$app->user->isGuest): ?>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    <?= Html::encode(Yii::$app->user->identity->display_name ?: Yii::$app->user->identity->username) ?>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <?= Html::a('<i class="fas fa-user mr-2"></i> โปรไฟล์', ['/users/view', 'id' => Yii::$app->user->id], ['class' => 'dropdown-item']) ?>
                    <div class="dropdown-divider"></div>
                    <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'dropdown-item']) ?>
                        <?= Html::submitButton('<i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ', ['class' => 'btn btn-link p-0 text-left w-100', 'style' => 'text-decoration: none; color: inherit;']) ?>
                    <?= Html::endForm() ?>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?= Url::to(['/site/index']) ?>" class="brand-link">
            <i class="fas fa-broom brand-image" style="font-size: 2rem; margin-left: 0.5rem; margin-right: 0.5rem; opacity: .8"></i>
            <span class="brand-text font-weight-light">CleaningWell</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <?php if (!Yii::$app->user->isGuest): ?>
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="fas fa-user-circle fa-2x text-white"></i>
                </div>
                <div class="info">
                    <a href="<?= Url::to(['/users/view', 'id' => Yii::$app->user->id]) ?>" class="d-block">
                        <?= Html::encode(Yii::$app->user->identity->display_name ?: Yii::$app->user->identity->username) ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <?= Html::a('<i class="nav-icon fas fa-tachometer-alt"></i><p>แดชบอร์ด</p>', ['/site/index'], ['class' => 'nav-link']) ?>
                    </li>

                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
                    <!-- Master Data -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                ข้อมูลหลัก
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>ลูกค้า</p>', ['/customers/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>โครงการ</p>', ['/projects/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>รายการสินค้า/บริการ</p>', ['/items/index'], ['class' => 'nav-link']) ?>
                            </li>
                        </ul>
                    </li>

                    <!-- Finance -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                            <p>
                                การเงิน
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>ใบเสนอราคา</p>', ['/quotations/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>ใบแจ้งหนี้</p>', ['/invoices/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>ใบเสร็จรับเงิน</p>', ['/receipts/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>ค่าใช้จ่าย</p>', ['/expenses/index'], ['class' => 'nav-link']) ?>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Housekeeper -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>
                                แม่บ้าน
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>จุดให้บริการ</p>', ['/service-points/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>กะการทำงาน</p>', ['/shifts/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>มอบหมายงาน</p>', ['/assignments/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>บันทึกเวลาทำงาน</p>', ['/attendances/index'], ['class' => 'nav-link']) ?>
                            </li>
                        </ul>
                    </li>

                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
                    <!-- Payroll -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-wallet"></i>
                            <p>
                                เงินเดือน
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>โครงสร้างเงินเดือน</p>', ['/wage-profiles/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>คำนวณเงินเดือน</p>', ['/payroll-runs/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>สลิปเงินเดือน</p>', ['/payslips/index'], ['class' => 'nav-link']) ?>
                            </li>
                        </ul>
                    </li>

                    <!-- Reports -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>
                                รายงาน
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>รายงานยอดคงเหลือ</p>', ['/reports/ar-aging'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>รายงานกระแสเงินสด</p>', ['/reports/cash-ledger'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>รายงานเงินเดือน</p>', ['/reports/payroll'], ['class' => 'nav-link']) ?>
                            </li>
                        </ul>
                    </li>

                    <!-- System -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                ระบบ
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>จัดการผู้ใช้</p>', ['/users/index'], ['class' => 'nav-link']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('<i class="far fa-circle nav-icon"></i><p>ตั้งค่าระบบ</p>', ['/settings/index'], ['class' => 'nav-link']) ?>
                            </li>
                        </ul>
                    </li>
                    <?php endif; ?>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= Html::encode($this->title) ?></h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <?php if (isset($this->params['breadcrumbs'])): ?>
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <?= Html::a('<i class="fas fa-home"></i> หน้าแรก', ['/site/index'], ['encode' => false]) ?>
                            </li>
                            <?php foreach ($this->params['breadcrumbs'] as $breadcrumb): ?>
                                <?php if (is_array($breadcrumb)): ?>
                                    <li class="breadcrumb-item">
                                        <?=@ Html::a($breadcrumb['label'], $breadcrumb['url'], ['encode' => isset($breadcrumb['encode']) ? $breadcrumb['encode'] : true]) ?>
                                    </li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active"><?= $breadcrumb ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                        <?php endif; ?>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?= $content ?>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; <?= date('Y') ?> <a href="#">CleaningWell</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div>
<!-- ./wrapper -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
