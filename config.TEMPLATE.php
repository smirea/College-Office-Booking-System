<?php

  session_start();
  
  define( 'DB_HOST', 'localhost' );
  define( 'DB_USER', '--ADD--YOUR--OWN--' );
  define( 'DB_PASS', '------------------' );
  define( 'DB_NAME', '------------------' );
  
  define( 'TABLE_ITEMS', 'items' );
  define( 'TABLE_BEAMER', 'beamer' );
  define( 'TABLE_CONFERENCEROOMKEYS', 'conferenceRoomKeys' );
  define( 'TABLE_BOARDGAMES', 'boardGames' );
  define( 'TABLE_BULK', 'bulk' );
  define( 'TABLE_SOUNDSYSTEM', 'soundSystem' );
  define( 'TABLE_BOOKS', 'books' );
  
  // default deadlines for different types of items.
  define( 'TIME_BULK',                   3 * 24 * 3600 );
  define( 'TIME_BOARDGAMES',             5 * 24 * 3600 );
  define( 'TIME_BEAMER',                 1 * 24 * 3600 );
  define( 'TIME_CONFERENCEROOMKEYS',     1 * 24 * 3600 ); 
  define( 'TIME_SOUNDSYSTEM',            2 * 24 * 3600 );
  define( 'TIME_BOOKS',                 30 * 24 * 3600 );
  
  define( 'URL_JPEOPLE', 'http://jpeople.code4fun.de/ajax.php' );
  
  define( 'LOGIN_ADMIN_NAME', 'admin' );
  define( 'LOGIN_ADMIN_PASS', 'rockingcollege' );
  // coma separated list of accepted campusnet accounts
  define( 'LOGIN_MODERATORS', LOGIN_ADMIN_NAME.',mandrejevi,cbaban,rdsouza,sjohnson,kkarki,skarki,ykiewitt,jmerlo,smirea,mpfingstho,jpfingstho,oseizov,ssheikh,vunnithan,lvujovic' );
  
  dbConnect( DB_USER, DB_PASS, DB_NAME, DB_HOST );
  
  function checkLogIn(){
    return isset( $_SESSION['username'] ) && isset( $_SESSION['loggedIn'] );
  }

  function goBack( array $args = null ){
    $url    = $_SERVER['HTTP_REFERER'];
    $pos    = strrpos( $url, '?' );
    $pos    = $pos !== false ? $pos : strlen($url);
    $query  = substr( $url, $pos + 1 );
    $back   = substr( $url, 0, $pos );
    parse_str( $query, $arr );
    if( $args ){
      foreach( $args as $k => $v ){
        $arr[$k] = $v;
      }
    }
    if( !$args || !isset($args['error']) ){
      unset( $arr['error'] );
    }
    $query = http_build_query( $arr );
    $query = $query ? "?$query" : $query;
    header("Location:$back$query");
  }
  
  function sqlToJsonOutput( $q ){
    if( $q ){
      if( $q !== true ){
        $a = array();
        while( $r = mysql_fetch_assoc( $q ) ){
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

  function setJsonHeaders(){
    if( !headers_sent() ){
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Content-type:application/json');
      header('Content-attributes: application/json; charset=ISO-8859-15');
      return true;
    } else {
      return false;
    }
  }
  
  function jsonOutput( array $arr ){
    setJsonHeaders();
    exit( json_encode( $arr ) );
  }

  function dbConnect($user, $pass, $name = null, $host = 'localhost'){
    $connexion = mysql_connect( $host, $user, $pass ) or die ("Could not connect to Data Base!");
    if( $name ) mysql_select_db( $name, $connexion ) or die ("Failed to select Data Base");
  }
	
?>
