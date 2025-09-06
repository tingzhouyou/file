<?php
// 数据库连接信息
$host = 'localhost';
$dbname = 'index2';
$username = 'index2';
$password = 'asdj1004488';

try {
    // 连接数据库
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>数据库连接成功</h2>";
    
    // 执行 SQL 语句添加字段
    $sql = "ALTER TABLE inspections 
            ADD COLUMN file_type VARCHAR(20) DEFAULT '站字号' COMMENT '文件类型',
            ADD COLUMN file_header VARCHAR(100) DEFAULT NULL COMMENT '文件编号'";
    
    $pdo->exec($sql);
    echo "<p style='color:green;'>成功添加 file_type 和 file_header 字段！</p>";
    
    // 验证字段是否添加成功
    $result = $pdo->query("DESCRIBE inspections");
    echo "<h3>表结构：</h3>";
    echo "<table border='1'><tr><th>字段</th><th>类型</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p>现在你可以 <a href='index.php'>返回主页</a> 并使用新功能了。</p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>错误</h2>";
    echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
    
    // 如果是字段已存在的错误，提供解决方案
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "<p>字段已经存在，无需重复添加。你可以 <a href='index.php'>返回主页</a> 并使用这些功能。</p>";
    } else {
        echo "<p>请检查数据库连接信息和 SQL 语句。</p>";
    }
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 20px;
    padding: 20px;
    max-width: 800px;
    margin: 0 auto;
}
h2 {
    color: #1a73e8;
}
table {
    border-collapse: collapse;
    width: 100%;
    margin: 20px 0;
}
th, td {
    padding: 8px;
    text-align: left;
}
th {
    background-color: #f2f2f2;
}
a {
    color: #1a73e8;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style> 