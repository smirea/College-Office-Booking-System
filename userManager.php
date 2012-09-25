<?php
	session_start();
	
	require_once('config.php');
	require_once('class.UserManager.php');

	$UM->register();
	$UM->showErrors();

	$UM->path = 'userManager.php';
	$UM->registerForm();
	if($_POST['user']) $UM->register();
	
?>
