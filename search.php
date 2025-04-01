<?php
header("Content-Type: application/json");

// 1. 社員番号の取得チェック
if (!isset($_GET['employee_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "社員番号が指定されていません"
    ]);
    exit;
}
$employeeId = $_GET['employee_id'];

// 2. Azure SQL DB 接続設定
$server   = "tcp:sqlsrv-foremployeedb.database.windows.net,1433"; // Azure SQL のFQDN
$database = "EmployeeDB";                                         // DB名
$user     = "sqladmin";                                           // SQL認証のユーザー名
$password = "Test1997726!";                                 // パスワード（.env管理でもOK）

$dsn = "odbc:Driver={ODBC Driver 17 for SQL Server};Server=$server;Database=$database";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. クエリ実行
    $stmt = $pdo->prepare("SELECT name FROM employee_data WHERE employee_id = ?");
    $stmt->execute([$employeeId]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo json_encode(["success" => true, "name" => $result['name']]);
    } else {
        echo json_encode(["success" => false, "message" => "該当する社員が見つかりませんでした。"]);
    }

} catch (PDOException $e) {
    error_log("【DB接続エラー】" . $e->getMessage());
    echo json_encode(["success" => false, "message" => "SQL Serverへの接続に失敗しました"]);
}
?>
