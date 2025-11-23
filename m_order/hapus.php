<?php
include "../../config.php";
session_start();
        $id = $_GET['id'];
        $oid = $_GET['oid'];
		
		$sql = "DELETE FROM m_kas WHERE id = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		
		if($stmt){		
			echo "<script>alert('Berhasil Menghapus'); document.location.href=('../../view/m_order/list.php?oid=$oid')</script>";
// 			echo "<script>alert('Berhasil Menghapus'); window.history.back();</script>";
		}else{
		    echo "<script>alert('Gagal Menghapus'); document.location.href=('../../view/m_order/list.php?oid=$oid')</script>";
// 			echo "<script>alert('Gagal Menghapus'); window.history.back();</script>";
		}
		

?>