<?php
include("../../config.php");   
$response = [];

if (isset($_POST['id'])) {

    // 1. Cek apakah sudah ada "pembayaran invoice" untuk order ini di m_kas
    $cek = $conn->prepare("
        SELECT COUNT(*) AS jml, nilai, shipper_id
        FROM m_kas 
        WHERE order_id = :order_id 
        AND nama = 'pembayaran invoice'
        LIMIT 1
    ");
    $cek->bindParam(':order_id', $_POST['id']);
    $cek->execute();
    $rowCek = $cek->fetch();

    // 2. Update m_order
    $query = $conn->prepare("
        UPDATE m_order SET
            invoice_no = :invoice_no,
            invoice_date = :invoice_date,
            credit_terms = :credit_terms,
            customer = :customer,
            attn = :attn,
            address = :address,
            total_amount = :total_amount,
            muncul_rek = :muncul_rek,
            rek_id = :rek_id,
            shipper_id = :shipper_id
        WHERE id = :id
    ");
    $query->bindParam(':invoice_no', $_POST['invoice_no']);
    $query->bindParam(':invoice_date', $_POST['invoice_date']);
    $query->bindParam(':credit_terms', $_POST['credit_terms']);
    $query->bindParam(':customer', $_POST['customer']);
    $query->bindParam(':attn', $_POST['attn']);
    $query->bindParam(':address', $_POST['address']);
    $query->bindParam(':total_amount', $_POST['total_amount']);
    $query->bindParam(':muncul_rek', $_POST['muncul_rek']);
    $query->bindParam(':rek_id', $_POST['rek_id']);
    $query->bindParam(':shipper_id', $_POST['shipper_id']);
    $query->bindParam(':id', $_POST['id']);

    if ($query->execute()) {
        // 3. Kalau belum ada data di m_kas → insert baru
        if ($rowCek['jml'] == 0) {
            $insertKas = $conn->prepare("
                INSERT INTO m_kas (nama, nilai, order_id, created_at, stat, shipper_id) 
                VALUES ('pembayaran invoice', :nilai, :order_id, NOW(), 4, :shipper_id)
            ");
            $insertKas->bindParam(':nilai', $_POST['total_amount']);
            $insertKas->bindParam(':order_id', $_POST['id']);
            $insertKas->bindParam(':shipper_id', $_POST['shipper_id']);
            $insertKas->execute();
        } else {
            // 4. Kalau sudah ada tapi nilainya atau shipper_id berbeda → update m_kas
            if (
                $rowCek['nilai'] != $_POST['total_amount'] ||
                $rowCek['shipper_id'] != $_POST['shipper_id']
            ) {
                $updateKas = $conn->prepare("
                    UPDATE m_kas 
                    SET nilai = :nilai,
                        shipper_id = :shipper_id
                    WHERE order_id = :order_id 
                    AND nama = 'pembayaran invoice'
                ");
                $updateKas->bindParam(':nilai', $_POST['total_amount']);
                $updateKas->bindParam(':shipper_id', $_POST['shipper_id']);
                $updateKas->bindParam(':order_id', $_POST['id']);
                $updateKas->execute();
            }
        }

        $response['code'] = 200;
    } else {
        $response['code'] = 505;
    }
}

echo json_encode($response);
?>






<?php
// include("../../config.php");   
// $response = [];

// if (isset($_POST['id'])) {

//     // 1. Cek apakah sudah ada "pembayaran invoice" untuk order ini di m_kas
//     $cek = $conn->prepare("
//         SELECT COUNT(*) AS jml, nilai
//         FROM m_kas 
//         WHERE order_id = :order_id 
//         AND nama = 'pembayaran invoice'
//         LIMIT 1
//     ");
//     $cek->bindParam(':order_id', $_POST['id']);
//     $cek->execute();
//     $rowCek = $cek->fetch();

//     // 2. Update m_order
//     $query = $conn->prepare("
//         UPDATE m_order SET
//             invoice_no = :invoice_no,
//             invoice_date = :invoice_date,
//             credit_terms = :credit_terms,
//             attn = :attn,
//             address = :address,
//             total_amount = :total_amount
//         WHERE id = :id
//     ");
//     $query->bindParam(':invoice_no', $_POST['invoice_no']);
//     $query->bindParam(':invoice_date', $_POST['invoice_date']);
//     $query->bindParam(':credit_terms', $_POST['credit_terms']);
//     $query->bindParam(':attn', $_POST['attn']);
//     $query->bindParam(':address', $_POST['address']);
//     $query->bindParam(':total_amount', $_POST['total_amount']);
//     $query->bindParam(':id', $_POST['id']);

//     if ($query->execute()) {
//         // 3. Kalau belum ada data di m_kas → insert baru
//         if ($rowCek['jml'] == 0) {
//             $insertKas = $conn->prepare("
//                 INSERT INTO m_kas (nama, nilai, order_id, created_at, stat) 
//                 VALUES ('pembayaran invoice', :nilai, :order_id, NOW(), 4)
//             ");
//             $insertKas->bindParam(':nilai', $_POST['total_amount']);
//             $insertKas->bindParam(':order_id', $_POST['id']);
//             $insertKas->execute();
//         } else {
//             // 4. Kalau sudah ada tapi nilainya berbeda → update nilai di m_kas
//             if ($rowCek['nilai'] != $_POST['total_amount']) {
//                 $updateKas = $conn->prepare("
//                     UPDATE m_kas 
//                     SET nilai = :nilai 
//                     WHERE order_id = :order_id 
//                     AND nama = 'pembayaran invoice'
//                 ");
//                 $updateKas->bindParam(':nilai', $_POST['total_amount']);
//                 $updateKas->bindParam(':order_id', $_POST['id']);
//                 $updateKas->execute();
//             }
//         }

//         $response['code'] = 200;
//     } else {
//         $response['code'] = 505;
//     }
// }

// echo json_encode($response);
?>