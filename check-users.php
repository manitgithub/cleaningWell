<?php

/**
 * Script to check users in database
 * Run: php check-users.php
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/console.php';
new yii\console\Application($config);

use app\models\User;

echo "🔍 ตรวจสอบผู้ใช้ในฐานข้อมูล...\n\n";

$users = User::find()->all();

if (empty($users)) {
    echo "❌ ไม่มีผู้ใช้ในฐานข้อมูล\n";
    echo "📝 สร้างผู้ใช้ admin...\n";
    
    $admin = new User();
    $admin->username = 'admin';
    $admin->password = 'admin123';
    $admin->display_name = 'ผู้ดูแลระบบ';
    $admin->email = 'admin@cleaningwell.com';
    $admin->role = User::ROLE_ADMIN;
    $admin->status = User::STATUS_ACTIVE;
    
    if ($admin->save()) {
        echo "✅ สร้าง admin สำเร็จ!\n";
    } else {
        echo "❌ ไม่สามารถสร้าง admin ได้:\n";
        print_r($admin->errors);
    }
} else {
    echo "✅ พบผู้ใช้ " . count($users) . " คน:\n\n";
    
    foreach ($users as $user) {
        echo "ID: {$user->id}\n";
        echo "Username: {$user->username}\n";
        echo "Display Name: {$user->display_name}\n";
        echo "Role: {$user->role} ({$user->getRoleName()})\n";
        echo "Status: {$user->status} ({$user->getStatusName()})\n";
        echo "Created: {$user->created_at}\n";
        echo "---\n";
    }
}

echo "\n🔑 ข้อมูลสำหรับ Login:\n";
echo "Admin: admin / admin123\n";
echo "Housekeeper: housekeeper1 / housekeeper123\n";
echo "\n🌐 URL: http://localhost/cleaningWell/web/index.php\n";
