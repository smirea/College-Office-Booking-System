<?php
  
  require_once 'config.php';
  require_once 'utils/campusnet.php';

  if( isset($_REQUEST['user']) && isset($_REQUEST['pass']) ){
    if( in_array(  $_REQUEST['user'], explode( ',', LOGIN_MODERATORS ) ) ){
      if( ($_REQUEST['user'] == LOGIN_ADMIN_NAME && $_REQUEST['pass'] == LOGIN_ADMIN_PASS) || loginToCampusNet( $_REQUEST['user'], $_REQUEST['pass'] ) ){
        $_SESSION['loggedIn'] = time();
        $_SESSION['username'] = $_REQUEST['user'];
        goBack();
      } else {
        goBack( array('error' => '* Invalid Credentials!') );
      }
    } else {
      goBack( array('error' => '* You do not have permission to access the system') ); 
    }
  } else {
    if( !checkLogIn() ){
      goBack( array('error' => '* Invalid Credentials!') );
    } else {
      goBack();
    }
  }
  
?>