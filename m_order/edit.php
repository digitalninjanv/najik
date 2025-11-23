<?php
	include("../../config.php");   
	$response = [];
	if (isset($_POST['id'])){
	    $query = "UPDATE m_order SET
        no_bastb = '".$_POST['no_bastb']."',
        container_seal = '".$_POST['container_seal']."',
        container_size = '".$_POST['container_size']."',
        loading_des = '".$_POST['loading_des']."',
        commodity = '".$_POST['commodity']."',
        vessel = '".$_POST['vessel']."',
        voyage = '".$_POST['voyage']."',
        conditi = '".$_POST['conditi']."',
        discharging_date = '".$_POST['discharging_date']."',
        status = '".$_POST['status']."'
        WHERE id = ".$_POST['id'];
		
		if ($conn->query($query)){
		    $response['code'] = 200;
		}else{
		    $response['code'] = 505;
		}
	}
	echo json_encode($response);
?>