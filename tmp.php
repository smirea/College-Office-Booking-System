<?php
require_once('config.php');

$q = "select account, fname, lname from jPeople.Search where lname in ('mirea', 'johnson', 'pfingsthorn', 'unnithan', 'seizov', 'vujovic', 'baban', 'dsouza', 'merlo', 'karki', 'kiewitt', 'sheikh', 'andrejevic')";

$q = mysql_query($q);
while( $r = mysql_fetch_assoc( $q ) ){ echo $r['account'].","; }

?>
