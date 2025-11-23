<?php
include '../../config.php';
error_reporting(0);

/* --- CEK SESI --- */
if(isset($_SESSION['email']) == 0) {
	header('Location: ../../index.php');
}

if($_SESSION['level_id'] == "1" || $_SESSION['level_id'] == "2"){
    // Allowed
} else {
  echo "<script>alert('Maaf! anda tidak bisa mengakses halaman ini '); document.location.href='../admin/'</script>";
  exit;
}

/* --- AMBIL DATA UTAMA --- */
$codx = $_GET['codx'];

// 1. Ambil Data Order (Digunakan di kedua halaman)
$sqlOrder = "SELECT * FROM m_order WHERE codx = :codx";
$stmtOrder = $conn->prepare($sqlOrder);
$stmtOrder->bindParam(':codx', $codx, PDO::PARAM_STR);
$stmtOrder->execute();
$rowOrder = $stmtOrder->fetch(PDO::FETCH_ASSOC);

// 2. Ambil Data Setting (Digunakan di halaman 2)
$sqlSetting = "SELECT * FROM setting ORDER BY id DESC";
$stmtSetting = $conn->prepare($sqlSetting);
$stmtSetting->execute();
$rowSetting = $stmtSetting->fetch();

// 3. Fungsi Terbilang (Digunakan di halaman 1)
function terbilang($angka) {
    $angka = abs($angka);
    $baca  = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $hasil = "";
    if ($angka < 12) { $hasil = $baca[$angka]; } 
    elseif ($angka < 20) { $hasil = terbilang($angka - 10) . " belas"; } 
    elseif ($angka < 100) { $hasil = terbilang(floor($angka / 10)) . " puluh " . terbilang($angka % 10); } 
    elseif ($angka < 200) { $hasil = "seratus " . terbilang($angka - 100); } 
    elseif ($angka < 1000) { $hasil = terbilang(floor($angka / 100)) . " ratus " . terbilang($angka % 100); } 
    elseif ($angka < 2000) { $hasil = "seribu " . terbilang($angka - 1000); } 
    elseif ($angka < 1000000) { $hasil = terbilang(floor($angka / 1000)) . " ribu " . terbilang($angka % 1000); } 
    elseif ($angka < 1000000000) { $hasil = terbilang(floor($angka / 1000000)) . " juta " . terbilang($angka % 1000000); } 
    else { $hasil = "Angka terlalu besar"; }
    return trim(preg_replace('/\s+/', ' ', $hasil));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Full Invoice Print</title>
<script>
  window.onload = function() {
    window.print();
  };
</script>
<style>
    /* --- GLOBAL STYLES --- */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background: #fff; 
    }

    /* --- HALAMAN 1 (FORMAL INVOICE) STYLES --- */
    .page-one .kop-surat {
        display: flex;
        align-items: flex-start;
        border-bottom: 2px solid black;
        padding-bottom: 10px;
    }
    .page-one .kop-logo img {
        width: 100px;
        height: auto;
    }
    .page-one .kop-text {
        margin-left: 15px;
    }
    .page-one .kop-text h1 {
        margin: 0;
        font-size: 30px;
        color: red;
        font-weight: bold;
    }
    .page-one .kop-text h2 {
        margin: 2px 0;
        font-size: 12px;
        color: blue;
        font-weight: bold;
    }
    .page-one .kop-text p {
        margin: 2px 0;
        font-size: 12px;
    }
    .page-one .watermark {
         z-index: -1;
         position: absolute;
         opacity: 0.05;
         width: 95%;
    }
    .page-one table {
        font-size: 14px;
    }

    /* --- HALAMAN 2 (RINCIAN) STYLES --- */
    .invoice-box {
      background: #fff;
      max-width: 100%; /* Full width saat print */
      margin: auto;
      padding: 10px;
      color: #555;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .invoice-box h1 {
      font-size: 36px;
      margin-bottom: 10px;
      color: #333;
    }
    .invoice-box .company-details {
      text-align: right;
    }
    .invoice-box .company-details p,
    .invoice-box .client-details p {
      margin: 0;
    }
    .invoice-box .client-details {
      margin-top: 30px;
    }
    .detail-table {
      width: 100%;
      line-height: inherit;
      text-align: left;
      margin-top: 20px;
      border-collapse: collapse;
    }
    .detail-table th {
      background: #eee;
      padding: 10px;
      font-weight: bold;
      border-bottom: 1px solid #ddd;
    }
    .detail-table td {
      padding: 10px;
      border-bottom: 1px solid #eee;
    }
    .total-section {
      text-align: right;
      padding-top: 20px;
    }
    .total-section h2 {
      margin: 0;
      color: #333;
    }

    /* --- PRINT CONTROL (PEMISAH HALAMAN) --- */
    @media print {
        .page-break {
            display: block;
            page-break-before: always; /* Memaksa pindah halaman */
        }
        body {
            background: none;
            padding: 0;
        }
        .invoice-box {
            box-shadow: none;
            border: none;
        }
    }
</style>
</head>
                
<body>

<div class="page-one">
    <div class="watermark">
         <img src="../../images/logo.png?p=1" width="100%">
    </div>

    <div class="kop-surat">
        <div class="kop-logo">
            <img src="../../images/logo.png?p=1" alt="Logo Perusahaan">
        </div>
        <div class="kop-text">
            <h1>PT. HARUKA JASA SAMUDRA</h1>
            <h2>FREIGH FORWARDING SERVICES, EXPORT-IMPORT, LAND TRANSPORT, WAREHOUSE</h2>
            <p><b>HEAD OFFICE</b> : Jl.Bengawan Solo No.40 Singkil. Kota Manado - SULUT 95234</p>
            <p><b>WAREHOUSE</b> : Jl.Ir.Soekarno Kelurahan Airmadidi Atas Kecamatan Airmadidi, Minahasa Utara – SULUT 95371</p>
            <p><b>SURABAYA BRANCH</b> : Jl.Laksda M.Nasir, Ruko TJ.Priok No.11. Perak Barat Kec. Krembangan Surabaya – JATIM 60177</p>
            <p>Telp: 0852-0011-2552 | Email: haruka.samudra@gmail.com</p>
        </div>
    </div>

    <br>
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
      <tr>
        <td style="width: 33.33%;"></td>
        <td style="width: 33.33%;"></td>
        <td style="width: 33.33%; padding: 10px; text-align: center; vertical-align: middle; border: 1px solid red; color: blue; font-weight: bold; text-transform: uppercase;">
          Commercial Invoice
        </td>
      </tr>
    </table>

    <table>
      <tr>
        <td>Invoice No</td><td>:</td><td><?php echo $rowOrder['invoice_no']; ?></td>
      </tr>
      <tr>
        <td>Invoice Date</td><td>:</td><td><?php echo $rowOrder['invoice_date']; ?></td>
      </tr>
      <tr>
        <td>Credit Terms</td><td>:</td><td><?php echo $rowOrder['credit_terms']; ?></td>
      </tr>
      <tr>
        <td>Customer</td><td>:</td><td><?php echo $rowOrder['customer']; ?></td>
      </tr>
      <tr>
        <td>Attn</td><td>:</td><td><?php echo $rowOrder['attn']; ?></td>
      </tr>
      <tr>
        <td>Address</td><td>:</td><td><?php echo $rowOrder['address']; ?></td>
      </tr>
    </table>

    <br><br>

    <table>
      <tr>
        <td>NO.BASTB</td><td>:</td><td><?php echo $rowOrder['no_bast']; ?></td>
      </tr>
      <tr>
        <td>CONTAINER/SEAL</td><td>:</td><td><?php echo $rowOrder['container_seal']; ?></td>
      </tr>
      <tr>
        <td>CONTAINER SIZE</td><td>:</td><td><?php echo $rowOrder['container_size']; ?></td>
      </tr>
      <tr>
        <td>LOADING/DESTINATION</td><td>:</td><td><?php echo $rowOrder['loading_des']; ?></td>
      </tr>
      
      <?php   
        // Ambil Data Shipper
        $shipperID = $rowOrder['shipper_id'];
        $stmtShipper = $conn->prepare("SELECT * FROM m_user WHERE id = :shipper");
        $stmtShipper->bindParam(':shipper', $shipperID, PDO::PARAM_INT);
        $stmtShipper->execute();
        $rowShipper = $stmtShipper->fetch(PDO::FETCH_ASSOC);
        
        // Ambil Data Consignee
        $consigneeID = $rowOrder['consignee_id'];
        $stmtConsignee = $conn->prepare("SELECT * FROM m_user WHERE id = :consignee");
        $stmtConsignee->bindParam(':consignee', $consigneeID, PDO::PARAM_INT);
        $stmtConsignee->execute();
        $rowConsignee = $stmtConsignee->fetch(PDO::FETCH_ASSOC);
      ?>
      
      <tr>
        <td>SHIPPER</td><td>:</td><td><?php echo $rowShipper['nama']; ?></td>
      </tr>
      <tr>
        <td>CONSIGNEE</td><td>:</td><td><?php echo $rowConsignee['nama']; ?></td>
      </tr>
      <tr>
        <td>COMMODITY</td><td>:</td><td><?php echo $rowOrder['commodity']; ?></td>
      </tr>
      <tr>
        <td>VESSEL</td><td>:</td><td><?php echo $rowOrder['vessel']; ?></td>
      </tr>
      <tr>
        <td>VOYAGE</td><td>:</td><td><?php echo $rowOrder['voyage']; ?></td>
      </tr>
      <tr>
        <td>CONDITION</td><td>:</td>
        <?php
        $conditiId = $rowOrder['conditi'];
        $stmtCond = $conn->prepare("SELECT nama FROM m_condition WHERE id = :id");
        $stmtCond->execute(['id' => $conditiId]);
        $condData = $stmtCond->fetch(PDO::FETCH_ASSOC);
        ?>
        <td><?php echo $condData['nama'] ?? ''; ?></td>
      </tr>
      <tr>
        <td>DISCHARGING DATE</td><td>:</td><td><?php echo $rowOrder['discharging_date']; ?></td>
      </tr>
    </table>

    <br>
    <table>
      <tr>
        <td >TOTAL AMOUNT</td>
        <td style="width:12px; text-align:center;">:</td>
        <td style="text-align:right; width:140px; font-variant-numeric:tabular-nums;"><?php echo "Rp " . number_format($rowOrder['total_amount'], 0, ',', '.'); ?></td>
      </tr>

      <tr>
        <td >Handling Container (VAT 1,1%)</td>
        <td style="width:12px; text-align:center;">:</td>
        <td style="text-align:right; width:140px; font-variant-numeric:tabular-nums;">
            <?php
            $persen = $rowOrder['total_amount'] * 0.011;
            echo "Rp " . number_format($persen, 0, ',', '.');
            ?>
        </td>
      </tr>

      <tr>
        <td style="text-align:left; padding:4px 6px; padding-right:10px; width:260px; font-weight:bold; border-top:1px solid #000; padding-top:8px;">TOTAL INVOICE</td>
        <td style="width:12px; text-align:center; font-weight:bold; border-top:1px solid #000; padding-top:8px;">:</td>
        <td style="text-align:right; width:140px; font-variant-numeric:tabular-nums; font-weight:bold; border-top:1px solid #000; padding-top:8px;">
            <?php
            $totalFinal = $rowOrder['total_amount'] + $persen;  
            echo "Rp " . number_format($totalFinal, 0, ',', '.');
            ?>
        </td>
      </tr>
    </table>

    <div style="margin-top:8px; font-style:italic;">
      IN WORD : <?php echo "Rp " . number_format($totalFinal, 0, ',', '.') . " (" . terbilang($totalFinal) . " rupiah)"; ?>
    </div>

    <br><br>

    <?php
    if ($rowOrder) {
        if ($rowOrder['muncul_rek'] == 1) {
            // Ambil data rekening
            $stmtRek = $conn->prepare("SELECT nama, des FROM m_rek WHERE id = :rekid");
            $stmtRek->execute(['rekid' => $rowOrder['rek_id']]);
            $rekData = $stmtRek->fetch(PDO::FETCH_ASSOC);
            ?>
            <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:14px;">
              <tr>
                <td style="vertical-align:top; width:50%;">
                  <div style="border:1px solid #000; padding:8px;">
                    <div><?php echo htmlspecialchars($rekData['nama']); ?></div>
                    <div><?php echo htmlspecialchars($rekData['des']); ?></div>
                    <div>Nomor Rekening</div>
                  </div>
                </td>
                <td style="vertical-align:top; text-align:center; width:50%;">
                  <div style="font-weight:bold;">REGARDS</div>
                  <div style="height:48px;"></div> 
                  <div style="font-weight:bold;">MEYDI MONA</div>
                  <div>FINANCE MANAGER</div>
                </td>
              </tr>
            </table>
            <?php
        } else {
            ?>
            <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:14px;">
              <tr>
                <td style="width:50%;">&nbsp;</td> 
                <td style="vertical-align:top; text-align:center; width:50%;">
                  <div style="font-weight:bold;">REGARDS</div>
                  <div style="height:48px;"></div> 
                  <div style="font-weight:bold;">MEYDI MONA</div>
                  <div>FINANCE MANAGER</div>
                </td>
              </tr>
            </table>
            <?php
        }
    }
    ?>
</div>
<div class="page-break"></div>

<div class="invoice-box">
    <table width="100%" style="border:none;">
      <tr style="border:none;">
        <td style="border:none;">
          <h1>INVOICE</h1>
          <h4>
              <?php
                $created_at = $rowOrder['created_at'];
                $timestamp = strtotime($created_at);
                echo " INVOICE #".$timestamp; 
                ?>
          </h4>
          <h5>
              <?php
                date_default_timezone_set('Asia/Makassar'); // WITA
                echo date('l, j F Y - H:i') . ' WITA';
                ?>
          </h5>
        </td>
        <td class="company-details" style="border:none;">
          <p><strong><?php echo $rowSetting['nama']; ?></strong></p>
          <p><?php echo $rowSetting['alamat']; ?></p>
          <p><?php echo $rowSetting['hp']; ?></p>
          <p><?php echo $rowSetting['email']; ?></p>
        </td>
      </tr>
    </table>

    <div class="client-details">
      <p><strong>Tagihan Kepada:</strong></p>
      <p><?php echo $rowOrder['customer']; ?></p>
      <p><?php echo $rowOrder['address']; ?></p>
    </div>

    <table class="detail-table">
      <tr>
        <th style="width:20px">No</th>
        <th>Deskripsi</th>
        <th>Qty</th>
        <th>Harga Satuan</th>
        <th>Total</th>
      </tr>
      
      <?php
       $count = 1;
       $grandTotalRincian = 0; 
        
       $sqlKas = $conn->prepare("SELECT * FROM `m_kas` WHERE order_codx = :order_codx AND stat = 4 ORDER BY id DESC");
       $sqlKas->bindParam(':order_codx', $codx, PDO::PARAM_STR);
       $sqlKas->execute();
       
       while($dataKas = $sqlKas->fetch()) {
           $nilai = $dataKas['nilai'];
           $grandTotalRincian += $nilai; 
       ?>
      <tr>
        <td><?php echo $count; ?></td>
        <td><?php echo $dataKas['nama'];?></td>
        <td>1</td>
        <td><?php echo "Rp " . number_format($dataKas['nilai'], 0, ',', '.');?></td>
        <td><?php echo "Rp " . number_format($dataKas['nilai'], 0, ',', '.');?></td>
      </tr>
        <?php 
        $count++;
        } 
        ?>
    </table>

    <div class="total-section">
      <h2>Total: <?php echo "Rp " . number_format($grandTotalRincian, 0, ',', '.'); ?></h2>
    </div>
</div>
</body>
</html>