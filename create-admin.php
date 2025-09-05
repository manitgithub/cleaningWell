<?php

/**
 * Script to create default admin user
 * Run: php create-admin.php
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/console.php';
new yii\console\Application($config);

use app\models\User;

// Check if admin user already exists
$adminUser = User::findByUsername('admin');

if (!$adminUser) {
    // Create admin user
    $adminUser = new User();
    $adminUser->username = 'admin';
    $adminUser->password = 'admin123';
    $adminUser->display_name = 'ผู้ดูแลระบบ';
    $adminUser->email = 'admin@cleaningwell.com';
    $adminUser->role = User::ROLE_ADMIN;
    $adminUser->status = User::STATUS_ACTIVE;
    
    if ($adminUser->save()) {
        echo "✅ สร้าง admin user สำเร็จ!\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        echo "❌ ไม่สามารถสร้าง admin user ได้\n";
        print_r($adminUser->errors);
    }
} else {
    echo "✅ admin user มีอยู่แล้ว\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
}

// Check if housekeeper user already exists
$housekeeperUser = User::findByUsername('housekeeper1');

if (!$housekeeperUser) {
    // Create housekeeper user
    $housekeeperUser = new User();
    $housekeeperUser->username = 'housekeeper1';
    $housekeeperUser->password = 'housekeeper123';
    $housekeeperUser->display_name = 'แม่บ้าน 1';
    $housekeeperUser->email = 'housekeeper1@cleaningwell.com';
    $housekeeperUser->role = User::ROLE_HOUSEKEEPER;
    $housekeeperUser->status = User::STATUS_ACTIVE;
    
    if ($housekeeperUser->save()) {
        echo "✅ สร้าง housekeeper user สำเร็จ!\n";
        echo "Username: housekeeper1\n";
        echo "Password: housekeeper123\n";
    } else {
        echo "❌ ไม่สามารถสร้าง housekeeper user ได้\n";
        print_r($housekeeperUser->errors);
    }
} else {
    echo "✅ housekeeper user มีอยู่แล้ว\n";
    echo "Username: housekeeper1\n";
    echo "Password: housekeeper123\n";
}

echo "\n🚀 ระบบพร้อมใช้งานแล้ว!\n";
echo "เข้าใช้งานที่: http://localhost/cleaningWell/web/index.php\n";
