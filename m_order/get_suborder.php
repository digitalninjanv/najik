<?php
include '../../config.php';

if (isset($_POST['order_kat_id'])) {
    $order_kat_id = $_POST['order_kat_id'];

    $stmt = $conn->prepare("SELECT id, nama FROM m_order_katsub WHERE order_kat_id = ?");
    $stmt->execute([$order_kat_id]);

    echo '<option value="">-- Pilih Sub Jenis Order --</option>';
    while ($row = $stmt->fetch()) {
        echo '<option value="'.$row['id'].'">'.$row['nama'].'</option>';
    }
}
?>
