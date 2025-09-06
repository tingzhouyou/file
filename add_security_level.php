<?php
// 数据库连接配置
require_once 'config.php';

// 设置页面标题和样式
$pageTitle = "数据库更新 - 添加文件密级字段";
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageTitle; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 30px;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #e2f3fd;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .btn {
            display: inline-block;
            background: #1a73e8;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #1557b0;
        }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $pageTitle; ?></h1>
        
        <?php
        // 执行数据库更新
        try {
            // 检查字段是否已存在
            $checkColumnQuery = "SHOW COLUMNS FROM inspections LIKE 'security_level'";
            $checkResult = $pdo->query($checkColumnQuery);
            
            if ($checkResult->rowCount() > 0) {
                // 字段已存在
                echo '<div class="result info">
                    <p><strong>信息:</strong> 文件密级字段 (security_level) 已经存在于数据库中。</p>
                </div>';
            } else {
                // 添加字段
                $alterQuery = "ALTER TABLE inspections ADD COLUMN security_level VARCHAR(50) DEFAULT NULL";
                $pdo->exec($alterQuery);
                
                echo '<div class="result success">
                    <p><strong>成功!</strong> 文件密级字段 (security_level) 已成功添加到数据库。</p>
                    <p>字段详情:</p>
                    <ul>
                        <li>字段名称: security_level</li>
                        <li>数据类型: VARCHAR(50)</li>
                        <li>默认值: NULL</li>
                    </ul>
                </div>';
            }
            
            // 显示SQL语句
            echo '<div class="info" style="margin-top: 20px;">
                <p><strong>执行的SQL语句:</strong></p>
                <pre>ALTER TABLE inspections ADD COLUMN security_level VARCHAR(50) DEFAULT NULL;</pre>
            </div>';
            
        } catch (PDOException $e) {
            echo '<div class="result error">
                <p><strong>错误:</strong> 无法更新数据库结构。</p>
                <p>错误信息: ' . $e->getMessage() . '</p>
            </div>';
        }
        ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn">返回主页</a>
        </div>
    </div>
</body>
</html> 