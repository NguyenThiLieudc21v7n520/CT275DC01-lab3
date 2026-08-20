<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
require_once __DIR__ . '/../libraries/Psr4AutoloaderClass.php';

$loader = new Psr4AutoloaderClass;
$loader->register();

// Các lớp có không gian tên bắt đầu với CT275\Labs nằm ở src/classes
$loader->addNamespace('CT275\Labs', __DIR__ . '/classes');

try {
    $PDO = (new CT275\Labs\PDOFactory())->create([
        'dbhost' => 'localhost',
        'dbname' => 'ct275_lab3',
        'dbuser' => 'postgres',
        'dbpass' => '123' // Thay đổi mật khẩu này nếu PostgreSQL của bạn dùng pass khác
    ]);
} catch (Exception $ex) {
    echo 'Không thể kết nối đến PostgreSQL, kiểm tra lại username/password đến PostgreSQL.<br>';
    exit("<pre>{$ex}</pre>");
}