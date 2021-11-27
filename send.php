<?php
header("Content-type:application/json");

if(!empty($_POST["name"]) && !empty($_POST["phone"]) and !empty($_POST["email"])){
	$files = [];
	if(!empty($_FILES)){
		foreach($_FILES["files"]["name"] as $k=>$val){
			$tmp_name = $_FILES["files"]["tmp_name"][$k];
			if(explode("/",mime_content_type($tmp_name))[0] == "image"){
				$parts = explode(".",$val);
				$new_name = time().".".$parts[count($parts)-1];
				move_uploaded_file($tmp_name,"uploads/".$new_name);
				$files[] = $new_name;
			}
		}
	}
	$mysqli = new mysqli("localhost","","","");
	$mysqli->query("INSERT INTO `requests` (`name`,`email`,`phone`,`created_at`) VALUES ('".$mysqli->real_escape_string($_POST["name"])."','".$mysqli->real_escape_string($_POST["email"])."','".$mysqli->real_escape_string($_POST["phone"])."',NOW())");

	$request_id = $mysqli->insert_id;
	if($files){
		$filename = '';
		$smtp = $mysqli->prepare("INSERT INTO `files` (`filename`,`request_id`) VALUES (?,".$request_id.")");
		$smtp->bind_param("s",$filename);
		foreach($files as $filename){
			$smtp->execute();
		}
	}
	die(json_encode(["status"=>"ok"]));
	
}
die(json_encode(["status"=>"err"]));