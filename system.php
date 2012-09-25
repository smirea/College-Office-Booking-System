<?php
  require_once 'config.php';
  
  $defaults = array(
    'view'  => 'bulk',
    'error' => ''
  );
  
  foreach( $defaults as $k => $v ){
    if( !isset( $_GET[$k] ) ){
      $_GET[$k] = $v;
    }
    $$k = $_GET[$k];
  }
  
  $ajaxParams = "view=$view";
?>

<!--[if lt IE 9.0]>
  <style>
    #main{
      display : none!important;
    }
  </style>
<![endif]-->

<link rel="stylesheet" href="css/jquery-ui.css" />
<link rel="stylesheet" href="css/main.css" />
<style>
  .ui-autocomplete {
    max-height  : 200px;
    width       : 260px;
    overflow-y  : auto;
    overflow-x  : hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 200px;
  }
  .ui-autocomplete .ui-menu-item{
    border-bottom : 1px solid #666;
  }
  .face{
    position  : absolute;
  }
  .face, .face table{
    font-family : verdana, arial, sans;
    font-size   : 9pt;
  }
  .face{
    border      : 1px solid #666;
    background  : #fff;
    width       : 300px;
  }
  .face .header{
    border-bottom : 1px solid #bbb;
    margin  : 5px;
  }
  .face .header .photo{
    float       : left;
    position    : relative;
    z-index     : 100;
    border      : 1px solid #666;
    background  : black;
    width       : 112px;
    height      : 112px;
    margin      : 0 5px 5px 0;
    text-align  : center;
    overflow    : hidden;
  }
  .face .header .photo img{
    max-width : 112px;
    max-height: 112px;
  }
  .face .header .name{
    font-size   : 11pt;
    font-weight : bold;
  }
  .face .header .majorLong{
    font-size : 10pt;
  }

  .face .body{
    margin  : 5px 5px 5px 10px;
  }
  .face .body td{
    vertical-align  : bottom;
  }
  .face .body .infoCell{
    font-weight : bold;
    text-align  : right;
  }

  .face .country{
    background  : lightblue;
    position    : relative;
    border-top  : 1px solid #666;
    margin-top  : 30px;
    padding     : 2px 0;
    text-indent : 10px;
  }
  .face .country img{
    position  : absolute;
    right     : 3px;
    bottom    : -2px;
  }
  .face .birthday .daysLeft{
    font-size   : 8pt;
  }
  .college-icon{
    display     : inline-block;
    background  : #666;
    border      : 1px solid #666;
    margin      : 1px;
    padding     : 0 3px 1px 3px;
    font-family : verdana, arial, courier;
    font-size   : 8pt;
    font-weight : bold;
    text-align  : center;
    color       : #fff;
  }
  .krupp{
    background : red;
  }
  .mercator{
    background : blue;
  }
  .college-iii{
    background : green;
  }
  .nordmetall{
    background  : yellow;
    color       : purple;
  }

</style>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript" src="js/tPopup.js"></script>
<script type="text/javascript" src="js/bookingSystem.js"></script>
<script type="text/javascript" src="js/main.js"></script> 

<?php 
  if( !checkLogIn() ){
    require_once 'display_loginForm.php';
  } else { 
?>

<script>
  $(function(){
    var viewToType = {
      'bulk'                : '',
      'bulkReturned'        : 'bulkReturned',
      'beamer'              : 'beamer',
      'conferenceRoomKeys'  : 'conferenceRoomKey',
      'boardGames'          : 'boardGame',
      'soundSystem'         : 'soundSystem',
      'books'               : 'book'
    };

<?php
  $ajaxPeople = URL_JPEOPLE;
  echo <<<JS
    var view = '$view';
    $('#bookingSystem').bookingSystem({
      ajaxFile    : 'ajax.php?$ajaxParams',
      ajaxPeople  : '$ajaxPeople',
      type        : viewToType[ view ]
    });
JS;
?>
    // Special setups for different views
    switch( view ){
      case 'bulkReturned':
        $('#bookingSystem #newItemTR').hide();
      break;
      case 'beamer':
        $('#bookingSystem #s_item').html( 'For what purpose?' );
      break;
      case 'conferenceRoomKeys':
        var input = $('#bookingSystem #item');
        var select = $( document.createElement('select') );
        select
          .attr({
            id        : input.attr('id'),
            tabindex  : input.attr('tabindex'),
            style     : input.attr('style')
          })
          .css({
            width : '100%'
          })
          .append('<option>Green Key</option>', '<option>Yellow Key</option>');
        input.replaceWith( select );
      break;
    }
  });
</script>

<div id="main">
  <?php require_once 'display_userBar.php'; ?>
  <div id="bookingSystem"></div>
</div> 

<?php } ?>