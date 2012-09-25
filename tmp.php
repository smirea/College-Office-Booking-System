<?php
require_once('config.php');

header('Content-type: text/plain');

$q = "select account, fname, lname from jPeople.Search where lname in ('mirea', 'johnson', 'pfingsthorn', 'unnithan', 'seizov', 'vujovic', 'baban', 'dsouza', 'merlo', 'karki', 'kiewitt', 'sheikh', 'andrejevic')";

$q = mysql_query($q);
//while( $r = mysql_fetch_assoc( $q ) ){ echo $r['account'].","; }

//flipDate('booked');
//flipDate('returned');

fixDate('booked');
//fixDate('returned');

function fixDate ($column) {
  $query = "SELECT * FROM items WHERE $column LIKE '____._._' ".
                                  "OR $column LIKE '____.__._' ".
                                  "OR $column LIKE '____._.__'";
  $query = mysql_query($query);
  while ($row = mysql_fetch_assoc($query)) {
    $arr = explode('.', $row[$column]);
    foreach ($arr as $key => $value) {
      if (strlen($value) == 1) {
        $arr[$key] = '0' . $arr[$key];
      }
    }
    $newDate = implode('.', $arr);
    //echo $row['id'] .'->'. $newDate;
    $q = "UPDATE items SET $column='$newDate' WHERE id='".$row['id']."'";
    if (!mysql_query($q)) {
      echo "[Error (id:".$row['id'].")] ".mysql_error() . "\n";
    }
  }
}

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
