<?php
include("../../config.php");
$response = [];

if (isset($_POST['id']) && isset($_POST['shipper_id'])) {
    $id = (int) $_POST['id'];
    $shipper_id = (int) $_POST['shipper_id'];

    try {
        $stmt = $conn->prepare("UPDATE m_order SET shipper_id = :shipper_id WHERE id = :id");
        $stmt->bindParam(':shipper_id', $shipper_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $response['code'] = 200;
        } else {
            $response['code'] = 505;
        }
    } catch (PDOException $e) {
        $response['code'] = 500;
        $response['error'] = $e->getMessage();
    }
}

echo json_encode($response);
?>
