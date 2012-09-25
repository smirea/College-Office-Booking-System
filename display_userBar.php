<?php
  $count = array( 'bulk', 'boardGames', 'conferenceRoomKeys', 'beamer', 'books', 'soundSystem' );
  $arr = array();
  foreach( $count as $v ){
    $c = mysql_num_rows( mysql_query( "SELECT * FROM $v WHERE returned=''" )
  );
    $class = $c === 0 ? 'good' : 'bad';
    switch( $v ){
      case 'beamer':
      case 'soundSystem':
        $c = $c === 0 ? 'in':'out'; 
        break;
      default: $c = "$c";
    }
    $arr[] = "$v:'<span class=\"$class\" style=\"font-weight:bold\">[$c]</span>'";
  }
  $arr = '{ '.implode( ', ', $arr ).' }';
  
  $view = isset($_GET['view']) ? $_GET['view'] : 'index';
  echo <<<JS
    <script type="text/javascript">
      $(function(){
        var countObj = $arr;
        $('#$view', $('#userInfo')).addClass('selected').removeAttr('href');
        for( var i in countObj ){
          $('#userInfo a.#'+i).append( countObj[i] );
        }
      });
    </script>
JS;
?>
<style>
  #userInfo .topLink{
    display       : inline-block;
    border-radius : 5px 5px 0 0;
    background    :rgba(255,255,255,0.8); 
    padding       :2px 10px;
  }
</style>
<div id="userInfo">
  <span class="topLink">
    Logged in as <b><?php echo $_SESSION["username"]; ?></b>
  </span>
  <a href="logout.php" class="topLink" style="float:right">log out</a>
  
  <div style="text-align:center; background:rgba(255,255,255,0.8);padding:2px">
    <a href="index.php" id="index">Index</a> |
    <a href="system.php?view=bulk" id="bulk">Booked</a> |
    <a href="system.php?view=boardGames" id="boardGames">Board Games</a> |
    <a href="system.php?view=conferenceRoomKeys" id="conferenceRoomKeys">Conference Room</a> |
    <a href="system.php?view=beamer" id="beamer">Beamer</a> |
    <a href="system.php?view=soundSystem" id="soundSystem">Sound System</a> |
    <a href="system.php?view=books" id="books">Books</a> |
    <a href="system.php?view=bulkReturned" id="bulkReturned">Returned</a>
  </div>
  
</div>
