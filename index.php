<?php
  require_once('config.php');
?>
<!--[if lt IE 9.0]>
  <style>
    #main{
      display : none!important;
    }
  </style>
  <div style="text-align:center;margin-top:50px;">
    <h3>GET A BETTER BROWSER!!!</h3>
    <img src="images/browsers.png" alt="Get a better brower man!" />
    <h3>IE >= 9 or firefox / chrome / opera / safari / etc</h3>
  </div>
<![endif]-->
<link rel="stylesheet" href="css/main.css" />

<script type="text/javascript" src="js/jquery.js"></script>

<style>
  .index img{
    border  : 0;
  }
  .item{
    display         : block;
    position        : relative;
    border          : 1px solid #666;
    background      : rgba( 255, 255, 255, 0.8 );
    margin          : 3px;
    text-decoration : none;
    color           : #333;
  }
  .item:hover{
    background  : rgba( 255, 255, 0, 0.8 );
  }
  .item .image{
    display       : block;
    float         : left;
    border-right  : 1px solid #666;
    padding       : 5px;
  }
  .item .image img{
    max-width : 64px;
  }
  .item .info{
    display : block;
    padding : 5px;
  }
  .item .title{
    display       : block;
    margin-bottom : 2px;
    font-size     : 11pt;
    font-weight   : bold;
  }
  .item .description{
    display     : block;
    font-style  : italic;
  }
  .item .meta .metaItem{
    display   : inline-block;
    margin    : 1px 3px 1px 0;
  }
  .item .meta .metaItem .value{
    font-size   : 10pt;
    font-weight : bold;
  }
  .item .meta .bookPeriod{
    position    : absolute;
    right       : 3px;
    bottom      : 2px;
  }
  .clearBoth{
    clear       : both;
    visibility  : hidden;
    overflow    : hidden;
    height      : 1px;
    line-height : 1px;
  }
</style>

<div id="main">
  <div class="index">

<?php 
  if( !checkLogIn() ){
    require_once 'display_loginForm.php';
  } else { 
    require_once 'display_userBar.php';
  
  
  function getCountPrint( $query ){
    if( $q = mysql_query( $query ) ){
      $c = mysql_num_rows( $q );
      $class = $c === 0 ? 'good' : 'bad';
      return "<span class=\"$class\">$c</span>";
    } else {
      return mysql_error();
    }
  }
  
  function getFirst( $query ){
    if( $q = mysql_query( $query ) ){
      return mysql_fetch_assoc( $q );
    } else {
      echo mysql_error();
      return null;
    }
  }
  
  function formatRetuerned( $b ){
    $val = '';
    if( !$b ){
      return '<span class="good">returned</span>';
    } else {
      return <<<HTML
        Booked on <span class="bad">$b[booked]</span> by <span class="bad">$b[user]</span>
HTML;
    }
  }
  
  function formatTime( $time ){
    if( $time < 24 * 3600 ){
      return ceil( $time / 3600 ) . ' hour(s)';
    } else {
      return ceil( $time / 24 / 3600 ) . ' day(s)';
    }
  }
  
  $info = array(
    'bulk' => array(
      'out'     => getCountPrint( "SELECT id FROM ".TABLE_BULK." WHERE returned=''" ),
      'overdue' => getCountPrint( "SELECT id FROM ".TABLE_BULK." WHERE timestamp < ".(time() - TIME_BULK) ),
      'total'   => getCountPrint( "SELECT id FROM ".TABLE_BULK )
    ),
    'boardGames' => array(
      'out'     => getCountPrint( "SELECT id FROM ".TABLE_BOARDGAMES." WHERE returned=''" ),
      'overdue' => getCountPrint( "SELECT id FROM ".TABLE_BOARDGAMES." WHERE timestamp < ".(time() - TIME_BOARDGAMES) )
    ),
    'beamer' => array(
      'last'  => getFirst( "SELECT * FROM ".TABLE_BEAMER." WHERE returned='' ORDER BY timestamp DESC LIMIT 0,1" )
    ),
    'conferenceRoomKeys' => array(
      'out'     => getCountPrint( "SELECT id FROM ".TABLE_CONFERENCEROOMKEYS." WHERE returned=''" ),
      'overdue' => getCountPrint( "SELECT id FROM ".TABLE_CONFERENCEROOMKEYS." WHERE timestamp < ".(time() - TIME_CONFERENCEROOMKEYS) )
    ),
    'soundSystem' => array(
      'last'  => getFirst( "SELECT * FROM ".TABLE_SOUNDSYSTEM." WHERE returned='' ORDER BY timestamp DESC LIMIT 0,1" )
    ),
    'books' => array(
      'out'     => getCountPrint( "SELECT id FROM ".TABLE_BOOKS." WHERE returned=''" ),
      'overdue' => getCountPrint( "SELECT id FROM ".TABLE_BOOKS." WHERE timestamp < ".(time() - TIME_BOOKS) )
    )
  );
  
  foreach( $info as $k => $v ){
    $$k = $v;
  }
  
  $beamer['last']       = formatRetuerned( $beamer['last'] );
  $soundSystem['last']  = formatRetuerned( $soundSystem['last'] );
  
  $time = array();
  $t    = array_keys($info);
  foreach( $t as $v ){
    eval('$time[$v] = TIME_'.strtoupper( $v ).';');
    $time[$v] = formatTime( $time[$v] );
  }
  
?>

<?php
  echo <<<HTML
    <a class="item" href="system.php?view=bulk">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td class="image"><img src="images/index_checkedOut.png" /></td>
          <td class="info">
            <span class="title">Checked out items</span>
            <span class="description">All items in the college office from bowls and pots to scissors and staplers</span>
            <span class="meta">
              <span class="metaItem" id="out">Out: <span class="value"> $bulk[out] </span></span>
              <span class="metaItem" id="overdue">Overdue: <span class="value"> $bulk[overdue] </span></span>
              <span class="metaItem bookPeriod">Book time: <b>$time[bulk]</b></span>
            </span>
          </td>
        </tr>
      </table>
    </a>

    <a class="item" href="system.php?view=boardGames">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td class="image"><img src="images/index_boardGames.png" /></td>
          <td class="info">
            <span class="title">Board Games</span>
            <span class="description">Also book board games here so we have an efficient way of tracking them down</span>
            <span class="meta">
              <span class="metaItem" id="out">Out: <span class="value"> $boardGames[out] </span></span>
              <span class="metaItem" id="overdue">Overdue: <span class="value"> $boardGames[overdue] </span></span>
              <span class="metaItem bookPeriod">Book time: <b>$time[boardGames]</b></span>
            </span>
          </td>
        </tr>
      </table>
    </a>

    <a class="item" href="system.php?view=conferenceRoomKeys">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td class="image"><img src="images/index_conferenceRoom_green.png" /></td>
          <td class="info">
            <span class="title">Conference Room Keys</span>
            <span class="description">Also sign conference room keys here for ease of access in terms of checking for availability</span>
            <span class="meta">
              <span class="metaItem" id="out">Out: <span class="value"> $conferenceRoomKeys[out] </span></span>
              <span class="metaItem" id="overdue">Overdue: <span class="value"> $conferenceRoomKeys[overdue] </span></span>
              <span class="metaItem bookPeriod">Book time: <b>$time[conferenceRoomKeys]</b></span>
            </span>
          </td>
        </tr>
      </table>
    </a>
    
    <a class="item" href="system.php?view=beamer">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td class="image"><img src="images/index_beamer.png" /></td>
          <td class="info">
            <span class="title">Beamer</span>
            <span class="description">Useful to have on online place to check the status of the beamer</span>
            <span class="meta">
              <span class="metaItem" id="out">Status: <span class="value"> $beamer[last] </span></span>
              <span class="metaItem bookPeriod">Book time: <b>$time[beamer]</b></span>
            </span>
          </td>
        </tr>
      </table>
    </a>

    <a class="item" href="system.php?view=soundSystem">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td class="image"><img src="images/index_soundSystem.png" /></td>
          <td class="info">
            <span class="title">Sound System</span>
            <span class="description">Mainly for the portable one</span>
            <span class="meta">
              <span class="metaItem" id="out">Status: <span class="value"> $soundSystem[last] </span></span>
              <span class="metaItem bookPeriod">Book time: <b>$time[soundSystem]</b></span>
            </span>
          </td>
        </tr>
      </table>
    </a>

    <a class="item" href="system.php?view=books">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td class="image"><img src="images/index_books.png" /></td>
          <td class="info">
            <span class="title">Books</span>
            <span class="description">Useful to have on online place to check the status of the beamer</span>
            <span class="meta">
              <span class="metaItem" id="out">Out: <span class="value"> $books[out] </span></span>
              <span class="metaItem" id="overdue">Overdue: <span class="value"> $books[overdue] </span></span>
              <span class="metaItem bookPeriod">Book time: <b>$time[books]</b></span>
            </span>
          </td>
        </tr>
      </table>
    </a>
    
    <a class="item" href="system.php?view=bulkReturned">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td class="image"><img src="images/index_returned.png" /></td>
          <td class="info">
            <span class="title">Returned items</span>
            <span class="description">All items ever given out and returned - you <b>can only view</b> all the items that were returned here</span>
            <span class="meta">
              <span class="metaItem" id="total">Total: <span class="value"> $bulk[total] </span></span>
            </span>
          </td>
        </tr>
      </table>
    </a>
    
  </div>

</div>

<table class="tPopup" id="tPopup">
  <tr><td align="center">
	<fieldset class="wrapper">
		<legend><a href="#" class="closePopup" id="closePopup">(X) close me</a></legend>
		<div class="title" id="title">Popup Title</div>
		<div class="description" id="description">This is a small simple and ermetic description of the popup</div>
		<form class="form" action="actions.php" method="post" id="form">
			<input type="hidden" name="action" id="action" value="" />
			<table>
				<tr>
					<td>From:</td>
					<td><input type="text" name="from" id="from" size="60" /></td>
				</tr>
				<tr>
					<td>To:</td>
					<td><input type="text" name="to" id="to" size="60" /></td>
				</tr>
				<tr>
					<td>Subject:</td>
					<td><input type="text" name="subject" id="subject" size="60" /></td>
				</tr>
				<tr>
					<td>Message:</td>
					<td><textarea name="message" rows="6" cols="55" id="message"></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<input type="button" id="submit" value="Send Email" />
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
  </td></tr>	
</table>
HTML;
?>

<?php } ?>