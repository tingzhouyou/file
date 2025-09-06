<?php
function getNextTypeId($pdo, $fileType) {
    try {
        if ($fileType === '站字号') {
            $result = $pdo->query("SELECT COALESCE(MAX(type_one_id), 0) as max_id FROM inspections WHERE file_type = '站字号'");
        } else {
            $result = $pdo->query("SELECT COALESCE(MAX(type_two_id), 0) as max_id FROM inspections WHERE file_type = '站参号'");
        }
        
        $maxId = $result->fetch(PDO::FETCH_ASSOC)['max_id'];
        return intval($maxId) + 1;
    } catch (PDOException $e) {
        return 1;
    }
}
?> 