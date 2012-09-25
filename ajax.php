<?php

  require_once 'config.php';
	require_once 'functions/mailFunctions.php';
	require_once 'class.UserManager.php';
	
	if( !checkLogIn() ){
    jsonOutput( array( '_loggedOut' => true ) );
	}
	
	foreach($_GET as $k => $v){
		if(is_string($v)) $_GET[$k] = addslashes($v);
			elseif(is_array($v)) {
				foreach($v as $k2 => $v2){
					$_GET[$k][$k2] = addslashes($v2);
				}
			}
	}
	
	/** These variables are used in more cases, therefore it easier to see if we define them only once **/
	$defaults = array(
    'view'    => TABLE_BULK,
    'select'  => '*',
    'orderBy' => 'booked, returned DESC',
    'max'     => 10
	);
	foreach( $defaults as $k => $v ){
    $_GET[$k] = isset( $_GET[$k] ) ? $_GET[$k] : $v;
	}
	$table    = $_GET['view'];
	$select	  = $_GET['select'];
	$orderBy  = $_GET['orderBy'];
	$max 		  = $_GET['max'];
	$username = $_SESSION['username'];
	
	switch($_GET['action']){
	
		case 'select':
//			$all = isset($_GET['all']) ? '' : " WHERE returned = ''";
			$where = isset($_GET['src']) ? 'WHERE item LIKE "%'.$_GET['src'].'%"' : '';
			
			$q = mysql_query("SELECT $select FROM $table $where ORDER BY $orderBy LIMIT 0, $max");
			sqlToJsonOutput_itemSelection($q);
		break;
		
		case 'filter':
			$values		= '';
			foreach($_GET['FILTER'] as $k => $v){
				$values .= " AND $k LIKE '%$v%'";
			}
			$values	= substr($values, 5);
			
			$q = mysql_query("SELECT $select FROM $table WHERE $values LIMIT 0,".$_GET['max']);
			sqlToJsonOutput_itemSelection($q);
		break;	
	
		case 'book':
      $booked = implode('.', array_reverse( explode('.', $_GET['booked']) ));
			$query =  "INSERT INTO $table(type, timestamp, item, booked, returned, phone, email, user, checkOutBy) VALUES
						( '".$_GET['type']."', '".time()."','".$_GET['item']."','".$booked."','".
              $_GET['returned']."','".$_GET['phone']."','".$_GET['email']."','".$_GET['user']."','$username')"; 
      $q = mysql_query($query);
			
      sqlToJsonOutput( $q );
		break;
		
		case 'delete':
			$q = mysql_query( "DELETE FROM $table WHERE id='".$_GET['id']."'" );
			sqlToJsonOutput( $q );
		break;
		
		case 'return':
			$q = mysql_query( "UPDATE $table SET returned='".date('d.m.Y')."', checkInBy='$username' WHERE id='".$_GET['id']."'" );
			sqlToJsonOutput( $q );
		break;
		
		case 'update':
			if(isset($_GET['VALUES'])){
				$condition	= isset($_GET['CONDITION']) ? $_GET['CONDITION'] : "id='".$_GET['id']."'";
				
				$values		= '';
				foreach($_GET['VALUES'] as $k => $v){
					$values .= ", $k='$v'";
				}
				$values	= substr($values, 2);
				
				$q = mysql_query( "UPDATE $table SET $values WHERE $condition" );
				sqlToJsonOutput( $q );
			} sqlToJsonOutput( array( 'error' => "ERROR: No values passed" ) );
		break;
		
		case 'autoComplete':
      setJsonHeaders();
      echo file_get_contents( URL_JPEOPLE."?action=fullAutoComplete&str=".$_GET['src'] );
		break;
		
		case 'remind':
//			echo sendMail($_GET['MAIL']['to'], $_GET['MAIL']['subject'], $_GET['MAIL']['from'], $_GET['MAIL']['message']) ? 'true' : 'Error sending mail';
		break;
		
		case 'checkLogin':
      jsonOutput( array( '_loggedOut' => !checkLogIn() ) );
		break;
		
	/*	
		case 'register':
			echo $UM->register($_GET['username'], $_GET['email'], $_GET['p1'], $_GET['p2']) ? 'true' : 'false';
		break;
		
		case 'logIn':
			echo $UM->logIn($_GET['username'], $_GET['password']) ? $_SESSION['username'] : 'false';
		break;
		
		case 'logOut': 
			$UM->logOut();
		break;
	*/
	}
  
  function sqlToJsonOutput_itemSelection( $q ){
    if( $q ){
      if( $q !== true ){
        $a = array();
        while( $r = mysql_fetch_assoc( $q ) ){
          if( isset($r['booked']) ){
            $r['booked'] = implode('.', array_reverse( explode('.', $r['booked']) ));
          }
          $a[] = $r;
        }
        jsonOutput( $a );
      } else {
        jsonOutput( array( 'success' => true ) );
      }
    } else {
      jsonOutput( array( 'error' => mysql_error() ) );
    }
  }
  
?>
