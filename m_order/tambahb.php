<?php
	include("../../config.php");  
	
	    $user_id = $_SESSION['user_id'];
	    $order_id = $_POST['order_id'];
	    $order_kat_id = $_POST['order_kat_id'];
	    $order_katsub_id = $_POST['order_katsub_id'];
	    $order_codx = $_POST['order_codx'];
	    $shipper_id = $_POST['shipper_id'];
	    $consignee_id = $_POST['consignee_id'];
		$nama = $_POST['nama'];
		$kat_kas_id = $_POST['kat_kas_id'];
		$nilai = $_POST['nilai'];
		$stat = $_POST['stat'];
		$created_at = $_POST['created_at'];
		$codx = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        
        try {
          $sql = "INSERT INTO m_kas SET
          user_id = :user_id,
          order_id = :order_id,
          order_kat_id = :order_kat_id,
          order_katsub_id = :order_katsub_id,
          order_codx = :order_codx,
          shipper_id = :shipper_id,
          consignee_id = :consignee_id,
          nama = :nama,
          kat_kas_id = :kat_kas_id,
          nilai = :nilai,
          stat = :stat,
          created_at = :created_at,
          codx = :codx"
          ;

          $stmt = $conn->prepare($sql);
          $stmt->bindParam(':user_id', $user_id);
          $stmt->bindParam(':order_id', $order_id);
          $stmt->bindParam(':order_kat_id', $order_kat_id);
          $stmt->bindParam(':order_katsub_id', $order_katsub_id);
          $stmt->bindParam(':order_codx', $order_codx);
          $stmt->bindParam(':shipper_id', $shipper_id);
          $stmt->bindParam(':consignee_id', $consignee_id);
          $stmt->bindParam(':nama', $nama);
          $stmt->bindParam(':kat_kas_id', $kat_kas_id);
          $stmt->bindParam(':nilai', $nilai);
          $stmt->bindParam(':stat', $stat);
          $stmt->bindParam(':created_at', $created_at);
          $stmt->bindParam(':codx', $codx);
          $stmt->execute();
        }
        
        catch(PDOException $e) {
          echo $e->getMessage();
        }
        
        if($stmt){		
			echo "<script>alert('Berhasil Tambah'); document.location.href=('index.php')</script>";
		}else{
			echo "<script>alert('Gagal Tambah'); document.location.href=('index.php')</script>";
		}
?>