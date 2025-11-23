<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
	header('Location: ../../index.php');
}

if( $_SESSION['level_id'] == "1" || $_SESSION['level_id'] == "2" ){
}else{
  echo "<script>alert('Maaf! anda tidak bisa mengakses halaman ini '); document.location.href='../admin/'</script>";
}

$codx = $_GET['codx'];

$sql = "SELECT * FROM m_order WHERE codx = :codx";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':codx', $codx, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$sqla = "SELECT * FROM setting ORDER BY id DESC";
$stmta = $conn->prepare($sqla);
$stmta->execute();
$rowa = $stmta->fetch();
?>





<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice</title>
  <script>
  window.onload = function() {
    window.print();
  };
</script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 20px;
      background: #f4f4f4;
    }

    .invoice-box {
      background: #fff;
      max-width: 800px;
      margin: auto;
      padding: 30px;
      border: 1px solid #eee;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      color: #555;
    }

    .invoice-box h1 {
      font-size: 36px;
      margin-bottom: 10px;
      color: #333;
    }

    .invoice-box .company-details {
      text-align: right;
      max-width: 50%;
      float: right;
      word-wrap: break-word;
    }


    .invoice-box .company-details p,
    .invoice-box .client-details p {
      margin: 0;
    }

    .invoice-box .client-details {
      margin-top: 30px;
    }

    table {
      width: 100%;
      line-height: inherit;
      text-align: left;
      margin-top: 20px;
      border-collapse: collapse;
    }

    table th {
      background: #eee;
      padding: 10px;
      font-weight: bold;
      border-bottom: 1px solid #ddd;
    }

    table td {
      padding: 10px;
      border-bottom: 1px solid #eee;
    }

    .total {
      text-align: right;
      padding-top: 20px;
    }

    .total h2 {
      margin: 0;
      color: #333;
    }

    @media print {
      body {
        background: none;
      }

      .invoice-box {
        box-shadow: none;
        border: none;
      }
    }
  </style>
</head>
<body>
  <div class="invoice-box">
    <table width="100%">
      <tr>
        <td>
          <h1>INVOICE</h1>
          <h4>
              <?php
                $created_at = $row['created_at']; // contoh: "2025-10-05 14:30:00"
                
                $timestamp = strtotime($created_at);
                
                echo " INVOICE #".$timestamp; // Output: 1759751400 (contoh output integer Unix timestamp)
                ?>
          </h4>
          <h5>
              <?php
                date_default_timezone_set('Asia/Makassar'); // WITA
                
                echo date('l, j F Y - H:i') . ' WITA';
                ?>
          </h5>
        </td>
        <td class="company-details">
          <p><strong><?php echo $rowa['nama']; ?></strong></p>
          <p><?php echo $rowa['alamat']; ?></p>
          <p><?php echo $rowa['hp']; ?></p>
          <p><?php echo $rowa['email']; ?></p>
        </td>
      </tr>
    </table>

    <div class="client-details">
      <p><strong>Tagihan Kepada:</strong></p>
      <p><?php echo $row['customer']; ?></p>
      <p><?php echo $row['address']; ?></p>
    </div>

    <table>
      <tr>
        <th style="width:20px">No</th>
        <th>Deskripsi</th>
        <th>Qty</th>
        <th>Harga Satuan</th>
        <th>Total</th>
      </tr>
      
      <?php
       $count = 1;
        $grandTotal = 0; // <-- variabel untuk menyimpan total
        
        $sql = $conn->prepare("SELECT * FROM `m_kas` WHERE order_codx = :order_codx AND stat = 4 ORDER BY id DESC");
        $sql->bindParam(':order_codx', $codx, PDO::PARAM_STR); // asumsi $ocodx adalah string
        $sql->execute();
	   
       
       $sql->execute();
       while($data=$sql->fetch()) {
           $nilai = $data['nilai'];
            $grandTotal += $nilai; 
       ?>
      <tr>
        <td><?php echo $count; ?></td>
        <td><?php echo $data['nama'];?></td>
        <td>1</td>
        <td><?php echo "Rp " . number_format($data['nilai'], 0, ',', '.');?></td>
        <td><?php echo "Rp " . number_format($data['nilai'], 0, ',', '.');?></td>
      </tr>
        <?php 
        $count=$count+1;
        } 
        ?>
    </table>

    <div class="total">
      <h2>Total: <?php echo "Rp " . number_format($grandTotal, 0, ',', '.'); ?></h2>
    </div>
  </div>
</body>
</html>


