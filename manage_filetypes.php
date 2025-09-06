<?php
session_start();
require_once 'config.php';

// 检查用户是否登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

// 处理添加文件类型
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add' && !empty($_POST['type_name'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO file_types (type_name) VALUES (?)");
            $stmt->execute([$_POST['type_name']]);
            $message = "文件类型添加成功！";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "添加失败：" . $e->getMessage();
            $messageType = "error";
        }
    } elseif ($_POST['action'] == 'delete' && !empty($_POST['type_id'])) {
        try {
            // 检查该类型是否在使用中
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM inspections WHERE file_type = (SELECT type_name FROM file_types WHERE id = ?)");
            $stmt->execute([$_POST['type_id']]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $message = "无法删除：该文件类型正在使用中";
                $messageType = "error";
            } else {
                $stmt = $pdo->prepare("DELETE FROM file_types WHERE id = ?");
                $stmt->execute([$_POST['type_id']]);
                $message = "文件类型删除成功！";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            $message = "删除失败：" . $e->getMessage();
            $messageType = "error";
        }
    }
}

// 获取所有文件类型
try {
    $fileTypes = $pdo->query("SELECT * FROM file_types ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $fileTypes = [];
    $message = "获取文件类型失败：" . $e->getMessage();
    $messageType = "error";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>文件类型管理</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .back-btn {
            padding: 8px 16px;
            background: #666;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        .btn-primary {
            background: #337ab7;
        }
        .btn-danger {
            background: #dc3545;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .delete-form {
            display: inline;
        }
        .delete-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 0;
        }
        .delete-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>文件类型管理</h1>
            <a href="index.php" class="back-btn">返回主页</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <input type="text" name="type_name" placeholder="输入新的文件类型名称" required>
            </div>
            <button type="submit" class="btn btn-primary">添加文件类型</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>文件类型</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fileTypes as $type): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($type['type_name']); ?></td>
                        <td><?php echo htmlspecialchars($type['created_at']); ?></td>
                        <td>
                            <form method="POST" action="" class="delete-form" onsubmit="return confirm('确定要删除这个文件类型吗？');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="type_id" value="<?php echo $type['id']; ?>">
                                <button type="submit" class="delete-btn">删除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 