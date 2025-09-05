<?php

/** @var yii\web\View $this */

$this->title = 'แดชบอร์ด';
$this->params['breadcrumbs'][] = [
    'label' => '<i class="fas fa-tachometer-alt"></i> ' . $this->title,
    'encode' => false,
];
?>
<div class="site-index">

    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">ลูกค้าทั้งหมด</span>
                    <span class="info-box-number">
                        0
                        <small>คน</small>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-project-diagram"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">โครงการ</span>
                    <span class="info-box-number">
                        0
                        <small>โครงการ</small>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-file-invoice"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Invoice คงค้าง</span>
                    <span class="info-box-number">
                        0
                        <small>ใบ</small>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">แม่บ้าน</span>
                    <span class="info-box-number">
                        0
                        <small>คน</small>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        รายได้รายเดือน
                    </h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div id="revenue-chart" style="height: 300px;">
                        <div class="text-center mt-5">
                            <h5 class="text-muted">กราฟรายได้จะแสดงที่นี่</h5>
                            <p class="text-muted">เมื่อมีข้อมูลการเงิน</p>
                        </div>
                    </div>
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </section>
        <!-- /.Left col -->

        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable">
            <!-- Map card -->
            <div class="card bg-gradient-primary">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        การเช็คอินแม่บ้าน
                    </h3>
                </div>
                <div class="card-body">
                    <div id="attendance-map" style="height: 250px;">
                        <div class="text-center text-white mt-5">
                            <h5>แผนที่การเช็คอินจะแสดงที่นี่</h5>
                            <p>เมื่อมีข้อมูลการเช็คอิน</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i>
                        กิจกรรมล่าสุด
                    </h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-info"><?= date('d M Y') ?></span>
                        </div>
                        <div>
                            <i class="fas fa-user bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> <?= date('H:i') ?></span>
                                <h3 class="timeline-header">
                                    <a href="#"><?= Yii::$app->user->identity->display_name ?? Yii::$app->user->identity->username ?></a>
                                    เข้าสู่ระบบ
                                </h3>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </section>
        <!-- right col -->
    </div>
    <!-- /.row (main row) -->

</div>
