<?php
  
  require_once 'config.php';

  unset( $_SESSION );
  session_destroy();
  
  goBack();
  
?>