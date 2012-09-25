<?php
require_once('config.php');

header('Content-type: text/plain');

$q = "select account, fname, lname from jPeople.Search where lname in ('mirea', 'johnson', 'pfingsthorn', 'unnithan', 'seizov', 'vujovic', 'baban', 'dsouza', 'merlo', 'karki', 'kiewitt', 'sheikh', 'andrejevic')";

$q = mysql_query($q);
//while( $r = mysql_fetch_assoc( $q ) ){ echo $r['account'].","; }

flipDate('booked');
flipDate('returned');

function flipDate ($column) {
  $query = mysql_query("SELECT * FROM items WHERE $column LIKE '%.2012' OR $column LIKE '%.2011'");
  while ($row = mysql_fetch_assoc($query)) {
    $flipped = implode('.', array_reverse(explode('.', $row[$column])));
    $q = "UPDATE items SET $column='$flipped' WHERE id='".$row['id']."'";
    if (mysql_query($q)) {
    } else {
      echo "[Error (id:".$row['id'].")] ".mysql_error() . "\n";
    }
  }
}

?>
