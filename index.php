<?php
session_start();
require_once 'config.php';
require_once 'get_next_type_id.php';  // 添加这一行来引入函数

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$messageType = '';

// 在页面加载时获取领导配置
try {
    $leaderConfig = [];
    $stmt = $pdo->query("SELECT position, name FROM leader_config");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $leaderConfig[$row['position']] = $row['name'];
    }
} catch (PDOException $e) {
    $leaderConfig = [];
}

// 处理保存、更新和删除操作
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'save') {
                // 首先检查文件编号是否已存在
                $checkFileHeader = $pdo->prepare("SELECT COUNT(*) FROM inspections WHERE file_header = ?");
                $checkFileHeader->execute([$_POST['file_header']]);
                $exists = $checkFileHeader->fetchColumn();

                if ($exists > 0) {
                    // 如果文件编号已存在，返回错误信息
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => '文件编号已存在，请检查后重新输入']);
                    exit;
                }

                // 获取表单提交的文件类型
                $fileType = $_POST['file_type'];
                $typeId = getNextTypeId($pdo, $fileType);
                
                // 处理领导签批状态，允许为空
                $leaderStatuses = [];
                for ($i = 1; $i <= 10; $i++) {
                    $status = isset($_POST["leader{$i}_status"]) && $_POST["leader{$i}_status"] !== '' 
                             ? $_POST["leader{$i}_status"] 
                             : null;
                    $leaderStatuses[] = $status;
                }
                
                $stmt = $pdo->prepare("INSERT INTO inspections (
                    check_time, inspector, inspected_unit, inspection_details, remarks,
                    leader1_status, leader2_status, leader3_status, leader4_status,
                    leader5_status, leader6_status, leader7_status, leader8_status,
                    leader9_status, leader10_status,
                    file_type, file_header,
                    leader1_name, leader2_name, leader3_name, leader4_name,
                    leader5_name, leader6_name, leader7_name, leader8_name,
                    leader9_name, leader10_name,
                    type_one_id, security_level
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $_POST['check_time'],
                    $_POST['inspector'],
                    $_POST['inspected_unit'],
                    $_POST['inspection_details'],
                    $_POST['remarks'],
                    $leaderStatuses[0],
                    $leaderStatuses[1],
                    $leaderStatuses[2],
                    $leaderStatuses[3],
                    $leaderStatuses[4],
                    $leaderStatuses[5],
                    $leaderStatuses[6],
                    $leaderStatuses[7],
                    $leaderStatuses[8],
                    $leaderStatuses[9],
                    $fileType,
                    $_POST['file_header'],
                    $_POST['leader1_name'],
                    $_POST['leader2_name'],
                    $_POST['leader3_name'],
                    $_POST['leader4_name'],
                    $_POST['leader5_name'],
                    $_POST['leader6_name'],
                    $_POST['leader7_name'],
                    $_POST['leader8_name'],
                    $_POST['leader9_name'],
                    $_POST['leader10_name'],
                    $typeId,
                    $_POST['security_level'] // 添加密级字段
                ]);
                
                // 保存成功后返回成功信息
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => '记录添加成功！']);
                exit;
                
                // 更新领导名字配置
                $stmt = $pdo->prepare("INSERT INTO leader_config (position, name) 
                                      VALUES (?, ?) 
                                      ON DUPLICATE KEY UPDATE name = VALUES(name)");
                                      
                for ($i = 1; $i <= 10; $i++) {
                    if (!empty($_POST["leader{$i}_name"])) {
                        $stmt->execute(["leader$i", $_POST["leader{$i}_name"]]);
                    }
                }
            } elseif ($_POST['action'] == 'update') {
                try {
                    // 处理领导签批状态，允许为空
                    $leaderStatuses = [];
                    for ($i = 1; $i <= 10; $i++) {
                        $status = isset($_POST["leader{$i}_status"]) && $_POST["leader{$i}_status"] !== '' 
                                 ? $_POST["leader{$i}_status"] 
                                 : null;
                        $leaderStatuses[] = $status;
                    }
                    
                    $stmt = $pdo->prepare("UPDATE inspections SET 
                        id = ?,
                        check_time = ?, 
                        inspector = ?, 
                        inspected_unit = ?, 
                        inspection_details = ?, 
                        remarks = ?,
                        leader1_status = ?, 
                        leader2_status = ?, 
                        leader3_status = ?, 
                        leader4_status = ?,
                        leader5_status = ?, 
                        leader6_status = ?, 
                        leader7_status = ?,
                        leader8_status = ?,
                        leader9_status = ?,
                        leader10_status = ?,
                        file_type = ?,
                        file_header = ?,
                        leader1_name = ?, 
                        leader2_name = ?, 
                        leader3_name = ?, 
                        leader4_name = ?,
                        leader5_name = ?, 
                        leader6_name = ?, 
                        leader7_name = ?,
                        leader8_name = ?,
                        leader9_name = ?,
                        leader10_name = ?,
                        security_level = ?
                        WHERE id = ?");
                    
                $stmt->execute([
                        $_POST['custom_id'], // 新的序号
                    $_POST['check_time'],
                    $_POST['inspector'],
                    $_POST['inspected_unit'],
                    $_POST['inspection_details'],
                    $_POST['remarks'],
                        $leaderStatuses[0],
                        $leaderStatuses[1],
                        $leaderStatuses[2],
                        $leaderStatuses[3],
                        $leaderStatuses[4],
                        $leaderStatuses[5],
                        $leaderStatuses[6],
                        $leaderStatuses[7],
                        $leaderStatuses[8],
                        $leaderStatuses[9],
                        $_POST['file_type'],
                        $_POST['file_header'],
                        $_POST['leader1_name'],
                        $_POST['leader2_name'],
                        $_POST['leader3_name'],
                        $_POST['leader4_name'],
                        $_POST['leader5_name'],
                        $_POST['leader6_name'],
                        $_POST['leader7_name'],
                        $_POST['leader8_name'],
                        $_POST['leader9_name'],
                        $_POST['leader10_name'],
                        $_POST['security_level'], // 添加密级字段
                    $_POST['id']
                ]);
                    
                $message = '记录更新成功！';
                $messageType = 'success';
                // 添加重定向，防止表单重复提交
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
                    
                    // 更新领导名字配置
                    $stmt = $pdo->prepare("INSERT INTO leader_config (position, name) 
                                          VALUES (?, ?) 
                                          ON DUPLICATE KEY UPDATE name = VALUES(name)");
                                          
                    for ($i = 1; $i <= 10; $i++) {
                        if (!empty($_POST["leader{$i}_name"])) {
                            $stmt->execute(["leader$i", $_POST["leader{$i}_name"]]);
                        }
                    }
                } catch (PDOException $e) {
                    $message = '操作失败：' . $e->getMessage();
                    $messageType = 'error';
                }
            } elseif ($_POST['action'] == 'delete') {
                $stmt = $pdo->prepare("DELETE FROM inspections WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $message = '记录删除成功！';
                $messageType = 'success';
                // 添加重定向，防止表单重复提交
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } elseif ($_POST['action'] == 'batch_delete') {
                if (isset($_POST['ids']) && is_array($_POST['ids'])) {
                    $placeholders = str_repeat('?,', count($_POST['ids']) - 1) . '?';
                    $stmt = $pdo->prepare("DELETE FROM inspections WHERE id IN ($placeholders)");
                    $stmt->execute($_POST['ids']);
                    $message = '成功删除 ' . count($_POST['ids']) . ' 条记录！';
                    $messageType = 'success';
                }
                // 添加重定向，防止表单重复提交
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    } catch (PDOException $e) {
        $message = '操作失败：' . $e->getMessage();
        $messageType = 'error';
    }
}

// 在获取记录之前添加分页逻辑
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20; // 每页显示20条
$offset = ($page - 1) * $perPage;

try {
    // 获取总记录数
    $countQuery = "SELECT COUNT(*) as total FROM inspections WHERE 1=1";
    $countParams = [];

    // 添加筛选条件到计数查询
    if (isset($_GET['inspector']) && !empty($_GET['inspector'])) {
        $countQuery .= " AND inspector LIKE ?";
        $countParams[] = '%' . $_GET['inspector'] . '%';
    }
    if (isset($_GET['inspected_unit']) && !empty($_GET['inspected_unit'])) {
        $countQuery .= " AND inspected_unit LIKE ?";
        $countParams[] = '%' . $_GET['inspected_unit'] . '%';
    }
    if (isset($_GET['file_type']) && !empty($_GET['file_type'])) {
        $countQuery .= " AND file_type LIKE ?";
        $countParams[] = '%' . $_GET['file_type'] . '%';
    }
    if (isset($_GET['file_header']) && !empty($_GET['file_header'])) {
        $countQuery .= " AND file_header LIKE ?";
        $countParams[] = '%' . $_GET['file_header'] . '%';
    }

    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($countParams);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $perPage);

    // 修改主查询，添加 LIMIT 和 OFFSET
    $query = "SELECT id, DATE_FORMAT(check_time, '%Y-%m-%d') as formatted_time, check_time, 
              inspector, inspected_unit, inspection_details, remarks, 
              leader1_status, leader2_status, leader3_status, leader4_status, 
              leader5_status, leader6_status, leader7_status, leader8_status,
              leader9_status, leader10_status,
              file_type,
              file_header,
              IFNULL(leader1_name, '张三') as leader1_name,
              IFNULL(leader2_name, '李四') as leader2_name,
              IFNULL(leader3_name, '王五') as leader3_name,
              IFNULL(leader4_name, '赵六') as leader4_name,
              IFNULL(leader5_name, '钱七') as leader5_name,
              IFNULL(leader6_name, '孙八') as leader6_name,
              IFNULL(leader7_name, '周九') as leader7_name,
              IFNULL(leader8_name, '吴十') as leader8_name,
              IFNULL(leader9_name, '郑十一') as leader9_name,
              IFNULL(leader10_name, '王十二') as leader10_name,
              type_one_id, security_level
              FROM inspections WHERE 1=1";
    $params = [];

    // 呈办人筛选
    if (isset($_GET['inspector']) && !empty($_GET['inspector'])) {
        $query .= " AND inspector LIKE ?";
        $params[] = '%' . $_GET['inspector'] . '%';
    }

    // 受检单位筛选
    if (isset($_GET['inspected_unit']) && !empty($_GET['inspected_unit'])) {
        $query .= " AND inspected_unit LIKE ?";
        $params[] = '%' . $_GET['inspected_unit'] . '%';
    }

    // 文件类型筛选
    if (isset($_GET['file_type']) && !empty($_GET['file_type'])) {
        $query .= " AND file_type LIKE ?";
        $params[] = '%' . $_GET['file_type'] . '%';
    }

    // 文件编号筛选
    if (isset($_GET['file_header']) && !empty($_GET['file_header'])) {
        $query .= " AND file_header LIKE ?";
        $params[] = '%' . $_GET['file_header'] . '%';
    }

    $query .= " ORDER BY id DESC LIMIT ?, ?";
    $params[] = intval($offset);  // 确保是整数
    $params[] = intval($perPage); // 确保是整数
    
    try {
    $stmt = $pdo->prepare($query);
        // 绑定所有参数
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value, PDO::PARAM_INT); // 明确指定为整数参数
        }
        $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = '获取记录失败：' . $e->getMessage();
        $messageType = 'error';
        $records = [];
    }
} catch (PDOException $e) {
    $message = '获取记录失败：' . $e->getMessage();
    $messageType = 'error';
    $records = [];
}

// 获取最大序号
try {
    $maxId = $pdo->query("SELECT MAX(id) as max_id FROM inspections")->fetch(PDO::FETCH_ASSOC)['max_id'];
    $nextId = $maxId ? $maxId + 1 : 1;
} catch (PDOException $e) {
    $nextId = 1;
}

function getStatusIcon($status) {
    if ($status === '已批') {
        return '<span class="status-text approved">是</span>';
    } elseif ($status === '未批') {
        return '<span class="status-text pending">否</span>';
    }
    return '<span class="status-text">-</span>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>文件编号登记管理系统</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            margin: 0;
            background: #0a192f;
            color: #fff;
            line-height: 1.6;
            position: relative;
            min-height: 100vh;
        }

        /* 动态背景 */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #0a192f 0%, #0c1b2b 100%);
        }

        .grid {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(26, 115, 232, 0.5);
            border-radius: 50%;
            animation: float 6s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-20px) translateX(10px); }
        }

        /* 修改主体容器样式 */
        .container {
            width: 100%;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: card-appear 0.5s ease-out;
        }

        @keyframes card-appear {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, 
                rgba(26, 115, 232, 0) 0%,
                rgba(26, 115, 232, 0.5) 50%,
                rgba(26, 115, 232, 0) 100%
            );
            animation: border-glow 3s infinite;
        }

        @keyframes border-glow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        .header h1 {
            color: #fff;
            font-size: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 .material-icons {
            color: #1a73e8;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .logout {
            background-color: rgba(220, 53, 69, 0.8);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout:hover {
            background-color: rgba(200, 35, 51, 0.9);
            transform: translateY(-1px);
        }

        /* 修改表格容器样式 */
        .table-container {
            width: 100%;
            overflow-x: auto;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* 修改表格样式 */
        .data-table {
            width: 100%;
            min-width: 1200px; /* 设置最小宽度确保内容不会挤压 */
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        th {
            background: rgba(26, 115, 232, 0.2);
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        th::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            animation: shine 3s infinite;
        }

        @keyframes shine {
            to {
                left: 100%;
            }
        }

        th:first-child {
            border-top-left-radius: 8px;
        }

        th:last-child {
            border-top-right-radius: 8px;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .inspection-details {
            width: 300px;
        }

        input[type="text"], 
        input[type="datetime-local"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            transition: all 0.3s;
        }

        input[type="text"]:focus, 
        input[type="datetime-local"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26,115,232,0.2);
            outline: none;
        }

        .edit-btn, .delete-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            white-space: nowrap;
        }

        .edit-btn {
            background: rgba(26, 115, 232, 0.1);
            color: #1a73e8;
        }

        .delete-btn {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 6px;
        }

        .edit-btn:hover {
            background: rgba(26, 115, 232, 0.2);
        }

        .delete-btn:hover {
            background: rgba(220, 53, 69, 0.2);
        }

        .edit-btn .material-icons,
        .delete-btn .material-icons {
            font-size: 18px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .material-icons {
            font-size: 18px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        /* 修改模态框样式 */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1a2942;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
        }

        /* 修改小型模态框（删除确认框）样式 */
        .modal-content.small {
            max-width: 400px;
        }

        /* 修复表单行样式 */
        .form-row {
            display: flex;
            gap: 20px;
            width: 100%;
        }

        /* 优化滚动条样式 */
        .modal-content::-webkit-scrollbar {
            width: 8px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: rgba(26, 115, 232, 0.5);
            border-radius: 4px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: rgba(26, 115, 232, 0.7);
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 20px;
        }

        .modal-btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 120px;
        }

        .confirm-btn {
            background: #1a73e8;
            color: white;
        }

        .confirm-btn:hover {
            background: #1557b0;
            transform: translateY(-1px);
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
        }

        .cancel-btn:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            animation: message-appear 0.3s ease-out;
        }
        @keyframes message-appear {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.success {
            background: rgba(40, 167, 69, 0.2);
            color: #98ff98;
            border: 1px solid rgba(40, 167, 69, 0.3);
            display: block;
        }

        .message.error {
            background: rgba(220, 53, 69, 0.2);
            color: #ff8080;
            border: 1px solid rgba(220, 53, 69, 0.3);
            display: block;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26,115,232,0.2);
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        /* 筛选区域样式 */
        .filter-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
            margin: 0;
        }

        .filter-group label {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
        }

        .filter-group label .material-icons {
            font-size: 16px;
            opacity: 0.8;
        }

        /* 修改筛选区域的下拉框样式 */
        .filter-input {
            padding: 8px 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            width: 100%;
            transition: all 0.3s;
            font-size: 14px;
            height: 38px;
        }

        /* 下拉框特定样式 */
        select.filter-input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            padding-right: 32px;
            cursor: pointer;
            background-color: rgba(255, 255, 255, 0.05);
        }

        select.filter-input option {
            background: #0a192f;
            color: #fff;
            padding: 8px;
        }

        /* 浅色主题适配 */
        body.light-theme .filter-input {
            border-color: rgba(0, 0, 0, 0.1);
            background: white;
            color: #333;
        }

        body.light-theme select.filter-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='%23333333'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
            background-color: white;
        }

        body.light-theme select.filter-input option {
            background: white;
            color: #333;
        }

        /* 悬停和焦点状态 */
        .filter-input:hover,
        .filter-input:focus {
            border-color: #1a73e8;
            outline: none;
            background: rgba(255, 255, 255, 0.1);
        }

        body.light-theme .filter-input:hover,
        body.light-theme .filter-input:focus {
            background: white;
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }

        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            color: white;
            background: rgba(26, 115, 232, 0.8);
        }

        .filter-btn:hover {
            background: rgba(26, 115, 232, 1);
        }

        .filter-btn.reset-btn {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.2);
            color: #333;
        }

        .filter-btn.reset-btn:hover {
            background: rgba(0, 0, 0, 0.15);
        }

        /* 深色模式下的重置按钮样式 */
        body:not(.light-theme) .filter-btn.reset-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        body:not(.light-theme) .filter-btn.reset-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .filter-btn .material-icons {
            font-size: 18px;
        }

        /* 主题切换按钮样式 */
        .theme-toggle {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .theme-toggle .material-icons {
            transition: transform 0.3s;
        }

        .theme-toggle:hover .material-icons {
            transform: rotate(180deg);
        }

        /* 浅色主题样式 */
        body.light-theme {
            background: #f5f7fa;
            color: #2c3e50;
        }

        body.light-theme .theme-toggle {
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #2c3e50;
        }

        body.light-theme .theme-toggle:hover {
            background: rgba(0, 0, 0, 0.08);
        }

        body.light-theme .background::before {
            background: linear-gradient(45deg, #f5f7fa 0%, #e4e7eb 100%);
        }

        body.light-theme .grid {
            background-image: 
                linear-gradient(rgba(26, 115, 232, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(26, 115, 232, 0.05) 1px, transparent 1px);
        }

        body.light-theme .card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        body.light-theme .header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        body.light-theme .header h1 {
            color: #2c3e50;
        }

        body.light-theme th {
            background: rgba(26, 115, 232, 0.1);
            color: #2c3e50;
        }

        body.light-theme td {
            color: #2c3e50;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        body.light-theme tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        body.light-theme .form-control,
        body.light-theme .filter-input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.2);
            color: #2c3e50;
        }

        body.light-theme .form-control:focus,
        body.light-theme .filter-input:focus {
            border-color: #1a73e8;
            background: #fff;
        }

        body.light-theme .form-control::placeholder,
        body.light-theme .filter-input::placeholder {
            color: rgba(0, 0, 0, 0.4);
        }

        body.light-theme .filter-section {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        body.light-theme .filter-group label {
            color: #2c3e50;
        }

        body.light-theme .modal-content {
            background: rgba(255, 255, 255, 0.95);
            color: #2c3e50;
        }

        body.light-theme .modal-content h3 {
            color: #1a73e8;
        }

        body.light-theme .modal-content label {
            color: #2c3e50;
        }

        body.light-theme .modal-content input,
        body.light-theme .modal-content textarea {
            color: #2c3e50;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.2);
        }

        body.light-theme .modal-content input:focus,
        body.light-theme .modal-content textarea:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26,115,232,0.2);
        }

        body.light-theme .modal-content input:disabled {
            background: #f5f5f5;
            color: #666;
        }

        body.light-theme .modal-content p {
            color: #2c3e50;
        }

        body.light-theme .message.success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        body.light-theme .message.error {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        body.light-theme .modal-content.small h3 {
            color: #2c3e50;
        }

        body.light-theme .modal-content.small p {
            color: #2c3e50;
        }

        body.light-theme .modal-content.small .modal-btn {
            color: #fff;
        }

        .approval-status {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .leader-status {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 14px;
        }

        .status-text {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 14px;
        }

        .status-text.approved {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .status-text.pending {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }

        .approval-inputs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .leader-input {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .leader-input label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        /* 在 <style> 标签内添加以下样式 */

        /* 分页样式 */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
            align-items: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .page-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            background: rgba(26, 115, 232, 0.1);
            border: 1px solid rgba(26, 115, 232, 0.2);
            border-radius: 6px;
            color: #1a73e8;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            min-width: 100px;
            justify-content: center;
        }

        .page-btn:hover {
            background: rgba(26, 115, 232, 0.2);
            transform: translateY(-1px);
        }

        .page-btn .material-icons {
            font-size: 18px;
        }

        .page-info {
            padding: 8px 16px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }

        /* 深色模式下的样式 */
        body:not(.light-theme) .page-info {
            color: rgba(255, 255, 255, 0.8);
        }

        /* 浅色模式下的样式 */
        body.light-theme .page-info {
            color: rgba(0, 0, 0, 0.7);
            background: rgba(0, 0, 0, 0.05);
        }

        body.light-theme .page-btn {
            background: rgba(26, 115, 232, 0.05);
            border: 1px solid rgba(26, 115, 232, 0.2);
        }

        body.light-theme .page-btn:hover {
            background: rgba(26, 115, 232, 0.1);
        }

        body.light-theme .pagination {
            background: rgba(0, 0, 0, 0.02);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* 版权信息 */
        .copyright {
            text-align: center;
            padding: 20px;
            color: rgba(255, 255, 255, 0.9);  /* 更亮的白色 */
            font-size: 12px;
            position: relative;
            z-index: 1;
            margin-top: 20px;
            font-weight: bold;  /* 加粗 */
        }

        /* 添加浅色主题下的版权样式 */
        body.light-theme .copyright {
            color: #ff0000 !important;  /* 红色 */
            font-weight: bold;
            text-shadow: none;  /* 移除发光效果 */
        }

        /* 移除原有的文件类型按钮样式 */
        .file-type-buttons,
        .file-type-btn,
        .edit-file-type-btn {
            display: none;
        }

        /* 操作栏样式 */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .left-actions, .right-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(26, 115, 232, 0.1);
            color: #1a73e8;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-btn.primary {
            background: #1a73e8;
            color: white;
        }

        .action-btn.danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .action-btn.danger:hover {
            background: rgba(220, 53, 69, 0.2);
        }

        /* 表格容器样式 */
        .table-container {
            overflow-x: auto;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: transparent;
        }

        .data-table th {
            background: rgba(26, 115, 232, 0.1);
            color: #1a73e8;
            font-weight: 500;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid rgba(26, 115, 232, 0.2);
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .data-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* 浅色主题适配 */
        body.light-theme .actions-bar {
            background: #f8f9fa;
        }

        body.light-theme .table-container {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        body.light-theme .data-table th {
            background: #f8f9fa;
            border-bottom-color: #dee2e6;
        }

        body.light-theme .data-table td {
            border-bottom-color: #dee2e6;
        }

        body.light-theme .data-table tr:hover {
            background: #f8f9fa;
        }

        /* 响应式优化 */
        @media (max-width: 768px) {
            .actions-bar {
                flex-direction: column;
                gap: 10px;
            }
            
            .left-actions, .right-actions {
                width: 100%;
                justify-content: center;
            }
            
            .table-container {
                margin: 0 -15px;
                border-radius: 0;
            }
        }

        /* 修改下拉框样式 */
        select.filter-input,
        select.form-control {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 8px center !important;
            padding-right: 32px !important;
            cursor: pointer;
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* 移除默认的下拉箭头 */
        select.filter-input::-ms-expand,
        select.form-control::-ms-expand {
            display: none;
        }

        /* 修复 Firefox 中的双箭头问题 */
        @-moz-document url-prefix() {
            select.filter-input,
            select.form-control {
                text-indent: 0;
                text-overflow: '';
                padding-right: 32px !important;
            }
        }

        /* 修复 Safari 中的双箭头问题 */
        @media screen and (-webkit-min-device-pixel-ratio:0) {
            select.filter-input,
            select.form-control {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='%23ffffff'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") !important;
            }
        }

        /* 浅色主题适配 */
        body.light-theme select.filter-input,
        body.light-theme select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='%23333333'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") !important;
            background-color: white;
        }

        /* 修改下拉框选项的样式 */
        .form-control option {
            background: #fff;
            color: #333;
            padding: 8px;
        }

        /* 深色主题下拉框样式 */
        body:not(.light-theme) .form-control {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* 确保深色主题下拉框选项依然保持黑色文字 */
        body:not(.light-theme) .form-control option {
            background: #fff;
            color: #333;
            padding: 8px;
        }

        /* 下拉框悬停和焦点状态 */
        .form-control:hover,
        .form-control:focus {
            border-color: #1a73e8;
            outline: none;
        }

        /* 深色主题下拉框悬停和焦点状态 */
        body:not(.light-theme) .form-control:hover,
        body:not(.light-theme) .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #1a73e8;
        }
    </style>
</head>
<body>
    <div class="background">
        <div class="grid"></div>
        <div class="particles">
            <?php for($i = 0; $i < 20; $i++): ?>
            <div class="particle" style="
                left: <?php echo rand(0, 100); ?>%;
                top: <?php echo rand(0, 100); ?>%;
                animation-delay: <?php echo $i * 0.3; ?>s;
            "></div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="header">
                <h1>
                    <span class="material-icons"></span>
                    文件编号登记管理系统 开发者:TingZhou_You  系统版本:V1.0
                </h1>
                <div style="display: flex; gap: 16px;">
                    <button onclick="showNewRecordForm()" class="edit-btn">
                        <span class="material-icons"></span>
                        新增记录
                    </button>
                    <a href="manage_filetypes.php" class="edit-btn" style="text-decoration: none;">
                        设置
                    </a>
                    <button onclick="toggleTheme()" class="theme-toggle" id="themeToggle">
                        <span class="material-icons"></span>
                        <span id="themeText">深色模式</span>
                    </button>
                    <a href="logout.php" class="logout">
                        <span class="material-icons"></span>
                        退出登录
                    </a>
                </div>
            </div>

            <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <!-- 筛选区域 -->
            <div class="filter-section">
                <form method="GET" class="filter-form">
                        <div class="filter-group">
                            <label>
                            <span class="material-icons"></span>
                                呈办人
                            </label>
                        <input type="text" name="inspector" class="filter-input" 
                            value="<?php echo isset($_GET['inspector']) ? htmlspecialchars($_GET['inspector']) : ''; ?>"
                            placeholder="请输入呈办人">
                        </div>
                        <div class="filter-group">
                            <label>
                            <span class="material-icons"></span>
                                文件名
                            </label>
                        <input type="text" name="inspected_unit" class="filter-input" 
                            value="<?php echo isset($_GET['inspected_unit']) ? htmlspecialchars($_GET['inspected_unit']) : ''; ?>"
                            placeholder="请输入文件名">
                        </div>
                        <div class="filter-group">
                            <label>
                            <span class="material-icons"></span>
                            文件类型
                            </label>
                            <select name="file_type" class="filter-input">
                                <option value="">全部</option>
                                <?php
                                // 获取所有文件类型
                                $fileTypesStmt = $pdo->query("SELECT type_name FROM file_types ORDER BY created_at");
                                while ($fileType = $fileTypesStmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = (isset($_GET['file_type']) && $_GET['file_type'] === $fileType['type_name']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($fileType['type_name']) . '" ' . $selected . '>' . 
                                         htmlspecialchars($fileType['type_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>
                            <span class="material-icons"></span>
                            文件编号
                            </label>
                        <input type="text" name="file_header" class="filter-input" 
                            value="<?php echo isset($_GET['file_header']) ? htmlspecialchars($_GET['file_header']) : ''; ?>"
                            placeholder="请输入文件编号">
                        </div>
                        <div class="filter-buttons">
                        <button type="submit" class="filter-btn">
                                <span class="material-icons"></span>
                                搜索
                            </button>
                        <a href="index.php" class="filter-btn reset-btn">
                                <span class="material-icons"></span>
                                重置
                            </a>
                    </div>
                </form>
            </div>

            <!-- 在表格上方的操作区域 -->
            <div class="actions-bar">
                <div class="left-actions">
                    <button onclick="showNewRecordForm()" class="action-btn primary">
                        <span class="material-icons"></span>
                        新增记录
                    </button>
                    <button onclick="showBatchDeleteConfirm()" class="action-btn danger">
                        <span class="material-icons"></span>
                        批量删除
                    </button>
                </div>
            </div>

            <!-- 表格头部 -->
            <div class="table-container">
                <table class="data-table">
                <thead>
                    <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                            </th>
                            <th style="width: 60px;">序号</th>
                            <th style="width: 100px;">时间</th>
                            <th style="width: 100px;">呈办人</th>
                            <th style="width: 120px;">文件类型</th>
                            <th style="width: 120px;">文件编号</th>
                            <th style="width: 150px;">文件名</th>
                            <th style="width: 200px;">主要内容</th>
                            <th style="width: 100px;">批示</th>
                            <th style="width: 100px;">密级</th>  <!-- 新增密级列 -->
                            <th style="width: 300px;">领导签批</th>
                            <th style="width: 120px; text-align: center;">操作</th>
                    </tr>
                </thead>
                <tbody id="recordsTable">
                    <?php foreach ($records as $record): ?>
                            <tr id="row_<?php echo $record['id']; ?>" data-file-type="<?php echo htmlspecialchars($record['file_type'] ?? '站字号'); ?>" data-file-header="<?php echo htmlspecialchars($record['file_header'] ?? ''); ?>">
                                <td style="text-align: center;">
                                    <input type="checkbox" name="record_ids[]" value="<?php echo $record['id']; ?>" class="record-checkbox" style="transform: scale(1.2);">
                                </td>
                        <td><?php echo $record['id']; ?></td>
                                <td data-original-time="<?php echo str_replace(' ', 'T', $record['check_time']); ?>"><?php echo $record['formatted_time']; ?></td>
                                <td><?php echo htmlspecialchars($record['inspector']); ?></td>
                                <td><?php echo htmlspecialchars($record['file_type'] ?? '站字号'); ?></td>
                                <td><?php echo htmlspecialchars($record['file_header'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['inspected_unit']); ?></td>
                        <td class="inspection-details"><?php echo nl2br(htmlspecialchars($record['inspection_details'])); ?></td>
                                <td><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($record['security_level'] ?? ''); ?></td>  <!-- 新增密级列 -->
                                <td>
                                    <div class="approval-status">
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <?php if (!empty($record["leader{$i}_status"])): ?>
                                                <div class="leader-status">
                                                    <span class="leader-name"><?php echo htmlspecialchars($record["leader{$i}_name"] ?? "领导{$i}"); ?>:</span>
                                                    <?php echo getStatusIcon($record["leader{$i}_status"]); ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    </td>
                            <td style="text-align: center;">
                            <div class="action-buttons">
                                <button onclick="editRecord(<?php echo $record['id']; ?>)" class="edit-btn">
                                    <span class="material-icons"></span>
                                    编辑
                                </button>
                                <button onclick="showDeleteConfirm(<?php echo $record['id']; ?>)" class="delete-btn">
                                    <span class="material-icons">删除</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- 新增记录表单 -->
    <div id="newRecordModal" class="modal">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto; padding: 15px 20px;">
            <h3 style="text-align: center; margin-bottom: 15px; color: #1a73e8; font-size: 18px;">新增记录</h3>
            
            <form id="newRecordForm" method="POST" style="display: grid; gap: 10px;">
                <input type="hidden" name="action" value="save">
                
                <div style="display: grid; grid-template-columns: 0.3fr 0.7fr 1fr; gap: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 13px; margin-bottom: 3px;">序号：</label>
                        <input type="number" name="custom_id" required class="form-control" value="<?php echo $nextId; ?>" min="1" style="padding: 6px 8px; height: 32px;">
                    </div>
                    <div class="form-group" style="flex: 0.7;">
                        <label style="font-size: 13px; margin-bottom: 3px;">时间：</label>
                        <input type="date" name="check_time" required class="form-control" id="new_check_time" style="padding: 6px 8px; height: 32px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 13px; margin-bottom: 3px;">呈办人：</label>
                        <input type="text" name="inspector" required class="form-control" style="padding: 6px 8px; height: 32px;">
                </div>
                    </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 13px; margin-bottom: 3px;">文件编号：</label>
                        <input type="text" name="file_header" class="form-control" placeholder="请输入文件编号" style="padding: 6px 8px; height: 32px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 13px; margin-bottom: 3px;">文件名：</label>
                        <input type="text" name="inspected_unit" required class="form-control" style="padding: 6px 8px; height: 32px;">
                </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; margin-bottom: 3px;">批示：</label>
                    <input type="text" name="remarks" class="form-control" style="padding: 6px 8px; height: 32px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; margin-bottom: 3px;">主要内容：</label>
                    <textarea name="inspection_details" rows="3" required class="form-control" 
                        style="min-height: 80px; padding: 6px 8px;" 
                        maxlength="500" 
                        oninput="this.nextElementSibling.textContent = '剩余字数：' + (500 - this.value.length)"></textarea>
                    <div style="text-align: right; font-size: 12px; color: #666;">剩余字数：500</div>
                </div>
                
                <div class="form-group">
                    <label style="font-size: 13px; margin-bottom: 3px;">文件类型：</label>
                    <select name="file_type" id="file_type_input" class="form-control" onchange="updateTypeNumber(this.value)" style="padding: 6px 8px; height: 32px;">
                        <?php
                        // 重新获取所有文件类型
                        $fileTypesStmt = $pdo->query("SELECT type_name FROM file_types ORDER BY created_at");
                        while ($fileType = $fileTypesStmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($fileType['type_name']) . '">' . 
                                 htmlspecialchars($fileType['type_name']) . '</option>';
                        }
                        ?>
                    </select>
                    <div id="type_number_display" style="margin-top: 5px; font-size: 12px; color: #666;"></div>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; margin-bottom: 3px;">签批情况：</label>
                    <div class="approval-inputs" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; margin-top: 5px;">
                        <!-- 领导1 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader1_name" placeholder="领导1" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="张三">
                                <span>：</span>
                            </div>
                            <select name="leader1_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <!-- 领导2 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader2_name" placeholder="领导2" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="李四">
                                <span>：</span>
                            </div>
                            <select name="leader2_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <!-- 领导3 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader3_name" placeholder="领导3" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="王五">
                                <span>：</span>
                        </div>
                            <select name="leader3_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <!-- 领导4 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader4_name" placeholder="领导4" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="赵六">
                                <span>：</span>
                            </div>
                            <select name="leader4_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <!-- 领导5 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader5_name" placeholder="领导5" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="钱七">
                                <span>：</span>
                        </div>
                            <select name="leader5_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <!-- 领导6 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader6_name" placeholder="领导6" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="孙八">
                                <span>：</span>
                            </div>
                            <select name="leader6_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <!-- 领导7 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader7_name" placeholder="领导7" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="周九">
                                <span>：</span>
                            </div>
                            <select name="leader7_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <!-- 领导8 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader8_name" placeholder="领导8" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="吴十">
                                <span>：</span>
                            </div>
                            <select name="leader8_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                <option value="已批">已批</option>
                                <option value="未批">未批</option>
                            </select>
                        </div>
                        <!-- 领导9 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader9_name" placeholder="领导9" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="郑十一">
                                <span>：</span>
                            </div>
                            <select name="leader9_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                <option value="已批">已批</option>
                                <option value="未批">未批</option>
                            </select>
                        </div>
                        <!-- 领导10 -->
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader10_name" placeholder="领导10" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="王十二">
                                <span>：</span>
                            </div>
                            <select name="leader10_status" class="form-control" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                <option value="已批">已批</option>
                                <option value="未批">未批</option>
                            </select>
                        </div>
                            </div>
                        </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; margin-bottom: 3px;">文件密级：</label>
                    <input type="text" name="security_level" class="form-control" placeholder="请输入文件密级" style="padding: 6px 8px; height: 32px;">
                </div>
                
                <div class="modal-buttons" style="margin-top: 15px; display: flex; justify-content: center; gap: 15px;">
                    <button type="submit" class="modal-btn confirm-btn" style="min-width: 100px; padding: 8px 15px; font-size: 14px;">
                        <span class="material-icons" style="font-size: 16px;"></span>
                        保存
                    </button>
                    <button type="button" onclick="closeNewRecordModal()" class="modal-btn cancel-btn" style="min-width: 100px; padding: 8px 15px; font-size: 14px;">
                        <span class="material-icons" style="font-size: 16px;"></span>
                        取消
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 编辑记录表单 -->
    <div id="editRecordModal" class="modal">
        <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto; padding: 15px 20px;">
            <h3 style="text-align: center; margin-bottom: 15px; color: #1a73e8; font-size: 18px;">编辑记录</h3>
            
            <form id="editRecordForm" method="POST" style="display: grid; gap: 10px;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_record_id">
                <input type="hidden" name="file_type" id="edit_file_type_input" value="">
                
                <div style="display: grid; grid-template-columns: 0.3fr 0.7fr 1fr; gap: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 13px; margin-bottom: 3px;">序号：</label>
                        <input type="number" name="custom_id" class="form-control" id="edit_id" style="padding: 6px 8px; height: 32px;" min="1">
                    </div>
                    <div class="form-group" style="flex: 0.7;">
                        <label style="font-size: 13px; margin-bottom: 3px;">时间：</label>
                        <input type="date" name="check_time" required class="form-control" id="edit_check_time" style="padding: 6px 8px; height: 32px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 13px; margin-bottom: 3px;">呈办人：</label>
                        <input type="text" name="inspector" required class="form-control" id="edit_inspector" style="padding: 6px 8px; height: 32px;">
                </div>
                    </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 13px; margin-bottom: 3px;">文件编号：</label>
                        <input type="text" name="file_header" id="edit_file_header" class="form-control" placeholder="请输入文件编号" style="padding: 6px 8px; height: 32px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 13px; margin-bottom: 3px;">文件名：</label>
                        <input type="text" name="inspected_unit" required class="form-control" id="edit_inspected_unit" style="padding: 6px 8px; height: 32px;">
                </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; margin-bottom: 3px;">批示：</label>
                    <input type="text" name="remarks" class="form-control" id="edit_remarks" style="padding: 6px 8px; height: 32px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; margin-bottom: 3px;">主要内容：</label>
                    <textarea name="inspection_details" rows="3" required class="form-control" id="edit_inspection_details" style="min-height: 80px; padding: 6px 8px;"></textarea>
                        </div>
                
                <div class="form-group">
                    <label style="font-size: 13px; margin-bottom: 3px;">文件类型：</label>
                    <select name="file_type" id="edit_file_type_input" class="form-control" style="padding: 6px 8px; height: 32px;">
                        <?php
                        // 重新获取所有文件类型
                        $fileTypesStmt = $pdo->query("SELECT type_name FROM file_types ORDER BY created_at");
                        while ($fileType = $fileTypesStmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($fileType['type_name']) . '">' . 
                                 htmlspecialchars($fileType['type_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; margin-bottom: 3px;">签批情况：</label>
                    <div class="approval-inputs" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; margin-top: 5px;">
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;">
                                <input type="text" name="leader1_name" id="edit_leader1_name" placeholder="领导1" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="张三">
                                <span>：</span>
                            </div>
                            <select name="leader1_status" class="form-control" id="edit_leader1_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader2_name" id="edit_leader2_name" placeholder="领导2" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="李四">
                                <span>：</span>
                            </div>
                            <select name="leader2_status" class="form-control" id="edit_leader2_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader3_name" id="edit_leader3_name" placeholder="领导3" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="王五">
                                <span>：</span>
                        </div>
                            <select name="leader3_status" class="form-control" id="edit_leader3_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader4_name" id="edit_leader4_name" placeholder="领导4" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="赵六">
                                <span>：</span>
                            </div>
                            <select name="leader4_status" class="form-control" id="edit_leader4_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader5_name" id="edit_leader5_name" placeholder="领导5" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="钱七">
                                <span>：</span>
                        </div>
                            <select name="leader5_status" class="form-control" id="edit_leader5_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader6_name" id="edit_leader6_name" placeholder="领导6" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="孙八">
                                <span>：</span>
                            </div>
                            <select name="leader6_status" class="form-control" id="edit_leader6_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader7_name" id="edit_leader7_name" placeholder="领导7" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="周九">
                                <span>：</span>
                            </div>
                            <select name="leader7_status" class="form-control" id="edit_leader7_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                    <option value="已批">已批</option>
                                    <option value="未批">未批</option>
                                </select>
                            </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader8_name" id="edit_leader8_name" placeholder="领导8" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="吴十">
                                <span>：</span>
                            </div>
                            <select name="leader8_status" class="form-control" id="edit_leader8_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                <option value="已批">已批</option>
                                <option value="未批">未批</option>
                            </select>
                        </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader9_name" id="edit_leader9_name" placeholder="领导9" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="郑十一">
                                <span>：</span>
                            </div>
                            <select name="leader9_status" class="form-control" id="edit_leader9_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                <option value="已批">已批</option>
                                <option value="未批">未批</option>
                            </select>
                        </div>
                        <div class="leader-input">
                            <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                                <input type="text" name="leader10_name" id="edit_leader10_name" placeholder="领导10" class="form-control" style="width: 80px; padding: 4px 8px; height: 30px;" value="王十二">
                                <span>：</span>
                            </div>
                            <select name="leader10_status" class="form-control" id="edit_leader10_status" style="width: 100%; padding: 3px 6px; height: 28px; font-size: 12px;">
                                <option value="">未选择</option>
                                <option value="已批">已批</option>
                                <option value="未批">未批</option>
                            </select>
                        </div>
                            </div>
                        </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="font-size: 13px; margin-bottom: 3px;">文件密级：</label>
                    <input type="text" name="security_level" id="edit_security_level" class="form-control" placeholder="请输入文件密级" style="padding: 6px 8px; height: 32px;">
                </div>
                
                <div class="modal-buttons" style="margin-top: 15px; display: flex; justify-content: center; gap: 15px;">
                    <button type="submit" class="modal-btn confirm-btn">
                        <span class="material-icons"></span>
                        保存
                    </button>
                    <button type="button" onclick="closeEditModal()" class="modal-btn cancel-btn">
                        <span class="material-icons"></span>
                        取消
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 删除确认对话框 -->
    <div id="deleteModal" class="modal">
        <div class="modal-content small">
            <h3 style="margin-bottom: 20px;">确认删除</h3>
            <p style="margin-bottom: 30px;">确定要删除这条记录吗？此操作不可撤销。</p>
            <div class="modal-buttons">
                <button onclick="confirmDelete()" class="modal-btn confirm-btn" style="background: #dc3545;">
                    <span class="material-icons"></span>
                    确认删除
                </button>
                <button onclick="closeDeleteModal()" class="modal-btn cancel-btn">
                    <span class="material-icons"></span>
                    取消
                </button>
            </div>
        </div>
    </div>

    <!-- 批量删除确认对话框 -->
    <div id="batchDeleteModal" class="modal">
        <div class="modal-content small">
            <h3 style="margin-bottom: 20px;">批量删除确认</h3>
            <p style="margin-bottom: 30px;">确定要删除选中的记录吗？此操作不可撤销。</p>
                        <div class="modal-buttons">
                <button onclick="confirmBatchDelete()" class="modal-btn confirm-btn" style="background: #dc3545;">
                    <span class="material-icons"></span>
                    确认删除
                </button>
                <button onclick="closeBatchDeleteModal()" class="modal-btn cancel-btn">
                    <span class="material-icons"></span>
                    取消
                </button>
                        </div>
                </div>
            </div>

    <!-- 在表格后添加分页导航 -->
    <div class="pagination" style="margin-top: 20px; display: flex; justify-content: center; gap: 10px; align-items: center;">
        <?php if ($totalPages > 1): ?>
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page - 1); ?><?php 
                    echo isset($_GET['inspector']) ? '&inspector=' . urlencode($_GET['inspector']) : '';
                    echo isset($_GET['inspected_unit']) ? '&inspected_unit=' . urlencode($_GET['inspected_unit']) : '';
                    echo isset($_GET['file_type']) ? '&file_type=' . urlencode($_GET['file_type']) : '';
                    echo isset($_GET['file_header']) ? '&file_header=' . urlencode($_GET['file_header']) : '';
                ?>" class="page-btn">
                    <span class="material-icons">chevron_left</span>
                    上一页
                </a>
            <?php endif; ?>
            
            <span class="page-info">第 <?php echo $page; ?> 页，共 <?php echo $totalPages; ?> 页 (共<?php echo $totalRecords; ?>条记录)</span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo ($page + 1); ?><?php 
                    echo isset($_GET['inspector']) ? '&inspector=' . urlencode($_GET['inspector']) : '';
                    echo isset($_GET['inspected_unit']) ? '&inspected_unit=' . urlencode($_GET['inspected_unit']) : '';
                    echo isset($_GET['file_type']) ? '&file_type=' . urlencode($_GET['file_type']) : '';
                    echo isset($_GET['file_header']) ? '&file_header=' . urlencode($_GET['file_header']) : '';
                ?>" class="page-btn">
                    下一页
                    <span class="material-icons">chevron_right</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
            </div>

    <!-- 版权信息 -->
    <div class="copyright" style="
        text-align: center;
        padding: 20px;
        color: rgba(255, 255, 255, 0.9);  /* 更亮的白色 */
        font-size: 12px;
        position: relative;
        z-index: 1;
        margin-top: 20px;
        font-weight: bold;  /* 加粗 */
    ">
        <p>Copyright © <?php echo date('Y'); ?> TingZhou_You 原创开发 版权所有 系统升级请微信联系 TingZhou_You</p>
        <p>文件编号登记管理系统 V1.0</p>
    </div>

    <!-- 修改版权样式 -->
    <style>
        /* 深色主题下的样式 */
        .copyright {
            color: rgba(255, 255, 255, 0.9) !important;  /* 更亮的白色 */
            font-weight: bold;
            text-shadow: 0 0 1px rgba(255, 255, 255, 0.3);  /* 添加轻微发光效果 */
        }

        /* 浅色主题下的样式 */
        body.light-theme .copyright {
            color: #1a73e8 !important;  /* 亮眼的蓝色 */
            font-weight: bold;
            text-shadow: 0 0 1px rgba(26, 115, 232, 0.3);  /* 添加蓝色发光效果 */
            text-shadow: none;  /* 移除发光效果 */
        }
    </style>

    <script>
    let currentDeleteId = null;

    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'block';
        // 使用 setTimeout 确保过渡效果正常工作
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }

    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
        // 等待过渡效果完成后再隐藏模态框
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    // 更新现有的模态框函数
    function showNewRecordForm() {
        showModal('newRecordModal');
        updateTypeNumber();
    }

    function closeNewRecordModal() {
        hideModal('newRecordModal');
    }

    function editRecord(id) {
        console.log('开始编辑记录:', id);
        try {
            // 获取行元素
            const row = document.querySelector(`tr[id="row_${id}"]`);
            if (!row) {
                console.error('找不到行元素:', id);
                return;
            }

            // 获取所有单元格
            const cells = row.cells;
            if (!cells || cells.length === 0) {
                console.error('行元素中没有单元格');
                return;
            }

            // 设置记录ID
            const recordIdInput = document.getElementById('edit_record_id');
            if (recordIdInput) {
                recordIdInput.value = id;
            }

            // 设置序号
            const idInput = document.getElementById('edit_id');
            if (idInput && cells[1]) {
                idInput.value = cells[1].textContent.trim();
            }

            // 设置时间
            const checkTimeInput = document.getElementById('edit_check_time');
            if (checkTimeInput && cells[2]) {
                const checkTime = cells[2].getAttribute('data-original-time');
                if (checkTime) {
                    const datePart = checkTime.split('T')[0];
                    checkTimeInput.value = datePart;
                }
            }

            // 设置呈办人
            const inspectorInput = document.getElementById('edit_inspector');
            if (inspectorInput && cells[3]) {
                inspectorInput.value = cells[3].textContent.trim();
            }

            // 设置文件类型
            const fileTypeSelect = document.getElementById('edit_file_type_input');
            if (fileTypeSelect && cells[4]) {
                const fileType = cells[4].textContent.trim();
                const options = fileTypeSelect.getElementsByTagName('option');
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === fileType) {
                        options[i].selected = true;
                    } else {
                        options[i].selected = false;
                    }
                }
            }

            // 设置文件编号
            const fileHeaderInput = document.getElementById('edit_file_header');
            if (fileHeaderInput && cells[5]) {
                fileHeaderInput.value = cells[5].textContent.trim();
            }

            // 设置文件名
            const inspectedUnitInput = document.getElementById('edit_inspected_unit');
            if (inspectedUnitInput && cells[6]) {
                inspectedUnitInput.value = cells[6].textContent.trim();
            }

            // 设置主要内容
            const inspectionDetailsInput = document.getElementById('edit_inspection_details');
            if (inspectionDetailsInput && cells[7]) {
                const content = cells[7].innerHTML
                    .replace(/<br\s*\/?>/g, '\n')
                    .replace(/&amp;/g, '&')
                    .replace(/&lt;/g, '<')
                    .replace(/&gt;/g, '>')
                    .replace(/&quot;/g, '"')
                    .trim();
                inspectionDetailsInput.value = content;
            }

            // 设置批示
            const remarksInput = document.getElementById('edit_remarks');
            if (remarksInput && cells[8]) {
                remarksInput.value = cells[8].textContent.trim();
            }

            // 设置密级
            const securityLevelInput = document.getElementById('edit_security_level');
            if (securityLevelInput && cells[9]) {
                securityLevelInput.value = cells[9].textContent.trim();
            }

            // 处理领导签批状态
            const approvalCell = cells[10];
            if (approvalCell) {
                // 重置所有领导状态
                for (let i = 1; i <= 10; i++) {
                    const nameInput = document.getElementById(`edit_leader${i}_name`);
                    const statusSelect = document.getElementById(`edit_leader${i}_status`);
                    if (nameInput && statusSelect) {
                        nameInput.value = nameInput.getAttribute('value') || '';
                        statusSelect.value = '';
                    }
                }

                // 获取并设置领导状态
                const leaderStatuses = approvalCell.querySelectorAll('.leader-status');
                leaderStatuses.forEach((status, index) => {
                    const leaderNameElement = status.querySelector('.leader-name');
                    const statusTextElement = status.querySelector('.status-text');

                    if (leaderNameElement && statusTextElement) {
                        const leaderName = leaderNameElement.textContent.replace(':', '').trim();
                        const statusText = statusTextElement.textContent.trim();

                        // 查找对应的领导输入框
                        for (let i = 1; i <= 10; i++) {
                            const nameInput = document.getElementById(`edit_leader${i}_name`);
                            const statusSelect = document.getElementById(`edit_leader${i}_status`);

                            if (nameInput && statusSelect) {
                                if (nameInput.value === leaderName || !nameInput.value) {
                                    nameInput.value = leaderName;
                                    statusSelect.value = statusText === '是' ? '已批' : 
                                                       statusText === '否' ? '未批' : '';
                                    break;
                                }
                            }
                        }
                    }
                });
            }

            // 显示编辑模态框
            showModal('editRecordModal');
            
        } catch (error) {
            console.error('编辑记录时出错:', error);
            alert('编辑记录时出现错误：' + error.message);
        }
    }

    function closeEditModal() {
        hideModal('editRecordModal');
    }

    function showDeleteConfirm(id) {
        currentDeleteId = id;
        showModal('deleteModal');
    }

    function closeDeleteModal() {
        hideModal('deleteModal');
        currentDeleteId = null;
    }

    // 更新点击模态框外部关闭的事件
    window.onclick = function(event) {
        const modals = ['newRecordModal', 'editRecordModal', 'deleteModal', 'batchDeleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target == modal) {
                hideModal(modalId);
            }
        });
    }

    // 添加自动隐藏消息的功能
    document.addEventListener('DOMContentLoaded', function() {
        const message = document.querySelector('.message');
        if (message) {
            setTimeout(function() {
                message.style.display = 'none';
            }, 3000);
        }
    });

    // 在页面加载时自动填充当前时间
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const currentDate = `${year}-${month}-${day}`;
        
        document.getElementById('new_check_time').value = currentDate;
    });

    // 主题切换优化
    function toggleTheme() {
        const body = document.body;
        const themeIcon = document.querySelector('#themeToggle .material-icons');
        const themeText = document.querySelector('#themeText');
        
        if (body.classList.contains('light-theme')) {
            body.classList.remove('light-theme');
            themeIcon.textContent = '';
            themeText.textContent = '深色模式';
            localStorage.setItem('theme', 'dark');
        } else {
            body.classList.add('light-theme');
            themeIcon.textContent = '';
            themeText.textContent = '浅色模式';
            localStorage.setItem('theme', 'light');
        }
    }

    // 页面加载时检查并应用保存的主题
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme');
        const themeIcon = document.querySelector('#themeToggle .material-icons');
        const themeText = document.querySelector('#themeText');
        
        // 默认使用浅色主题
        if (savedTheme === 'dark') {
            themeIcon.textContent = '';
            themeText.textContent = '深色模式';
        } else {
            // 默认或 'light' 时使用浅色主题
            document.body.classList.add('light-theme');
            themeIcon.textContent = '';
            themeText.textContent = '浅色模式';
            localStorage.setItem('theme', 'light');
        }
    });

    // 新增记录的类型切换函数
    function switchFileType(button, type) {
        // 移除所有按钮的活动状态
        document.querySelectorAll('.file-type-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        // 添加当前按钮的活动状态
        button.classList.add('active');
        // 设置隐藏输入框的值
        document.getElementById('file_type_input').value = type;
        // 更新序号显示
        updateTypeNumber(type);
    }

    // 编辑记录的类型切换函数
    function switchEditFileType(button, type) {
        // 移除所有按钮的活动状态
        document.querySelectorAll('.edit-file-type-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        // 添加当前按钮的活动状态
        button.classList.add('active');
        // 设置隐藏输入框的值
        document.getElementById('edit_file_type_input').value = type;
    }

    // 更新文件类型值
    function updateFileType(input, targetId) {
        const type = input.value.trim() || input.getAttribute('data-default') || '站字号';
        document.getElementById(targetId).value = type;
        
        // 更新父元素的 data-type 属性
        input.closest('.file-type-option').setAttribute('data-type', type);
    }

    // 更新编辑模式下的文件类型值
    function updateEditFileType(input, targetId) {
        const type = input.value.trim() || input.getAttribute('data-default') || '站字号';
        document.getElementById(targetId).value = type;
        
        // 更新父元素的 data-type 属性
        input.closest('.file-type-option').setAttribute('data-type', type);
    }

    // 在页面加载时添加事件监听器，防止点击输入框时触发父元素的点击事件
    document.addEventListener('DOMContentLoaded', function() {
        // 为所有类型名称输入框添加事件监听器
        document.querySelectorAll('.type-name-input').forEach(input => {
            input.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    });

    // 表格选择优化
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const checkboxes = document.getElementsByClassName('record-checkbox');
        const checked = selectAllCheckbox.checked;
        
        Array.from(checkboxes).forEach(checkbox => {
            checkbox.checked = checked;
            const row = checkbox.closest('tr');
            if (row) {
                checked ? row.classList.add('selected') : row.classList.remove('selected');
            }
        });
    }

    // 单行选择处理
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.record-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                if (row) {
                    this.checked ? row.classList.add('selected') : row.classList.remove('selected');
                }
            });
        });
    });

    // 显示批量删除确认框
    function showBatchDeleteConfirm() {
        const selectedCheckboxes = document.querySelectorAll('.record-checkbox:checked');
        if (selectedCheckboxes.length === 0) {
            alert('请至少选择一条记录');
            return;
        }
        showModal('batchDeleteModal');
    }

    // 关闭批量删除确认框
    function closeBatchDeleteModal() {
        hideModal('batchDeleteModal');
    }

    // 确认批量删除
    function confirmBatchDelete() {
        const selectedCheckboxes = document.querySelectorAll('.record-checkbox:checked');
        const ids = Array.from(selectedCheckboxes).map(cb => cb.value);
        
        // 创建表单并提交
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="batch_delete">
            ${ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
        `;
        document.body.appendChild(form);
        form.submit();
    }

    function switchFileType(button, type) {
        // 移除所有按钮的活动状态
        document.querySelectorAll('.file-type-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        // 添加当前按钮的活动状态
        button.classList.add('active');
        // 设置隐藏输入框的值
        document.getElementById('file_type_input').value = type;
        // 更新序号显示
        updateTypeNumber(type);
    }

    // 更新类型序号显示
    function updateTypeNumber(type) {
        const records = <?php echo json_encode($records); ?>;
        let maxNumber = 0;
        
        records.forEach(record => {
            if (record.file_type === type) {
                const number = type === '站字号' ? parseInt(record.type_one_id) : parseInt(record.type_two_id);
                if (number && number > maxNumber) {
                    maxNumber = number;
                }
            }
        });
        
        // 显示下一个序号
        document.getElementById('type_number_display').textContent = `${type}序号: ${maxNumber + 1}`;
    }

    // 编辑记录的类型切换函数
    function switchEditFileType(button, type) {
        // 移除所有按钮的活动状态
        document.querySelectorAll('.edit-file-type-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        // 添加当前按钮的活动状态
        button.classList.add('active');
        // 设置隐藏输入框的值
        document.getElementById('edit_file_type_input').value = type;
    }

    // 在页面加载时初始化
    document.addEventListener('DOMContentLoaded', function() {
        // 初始化新增记录的文件类型
        const typeOneBtn = document.getElementById('type_one');
        if (typeOneBtn) {
            typeOneBtn.classList.add('active');
            updateTypeNumber('站字号');
        }
    });

    // 修改表单提交处理
    document.getElementById('newRecordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // 创建 FormData 对象
        const formData = new FormData(this);
        
        // 发送 AJAX 请求
        fetch('index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'error') {
                // 显示错误提示框
                alert(data.message);
            } else {
                // 保存成功，刷新页面
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('保存失败，请重试');
        });
    });
    </script>
</body>
</html>

