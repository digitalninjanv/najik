<?php
	include("../../config.php");   
	$response = [];
	if (isset($_POST['id'])){
	    $query = "UPDATE m_kas SET
	    stat = '".$_POST['stat']."',
	    created_at = '".$_POST['created_at']."',
	    nama = '".$_POST['nama']."',
	    nilai = '".$_POST['nilai']."'
	    WHERE id = ".$_POST['id'];
		
		if ($conn->query($query)){
		    $response['code'] = 200;
		}else{
		    $response['code'] = 505;
		}
	}
	echo json_encode($response);
?>