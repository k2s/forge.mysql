<?php
/**
 * This script will generate 3 files (fk_drop.sql, fk_create.sql, convert.sql) 
 * into $output_path.
 * It will not modify your database when you run it. You have to 
 * manualy execute the generated scripts to your database in this order:
 * 1. fk_drop.sql - will drop all references between tables
 * 2. convert.sql - (optional) will convert tables to defined char. set
 * 3. fk_create.sql - this will create the references back
 * 
 * The reason for this script is that if you use (var)char fields in references 
 * and you need to change character set or colation you will experience error:
 * `Error Code : 1025 Error on rename of '...' to '...' (errno: 150)`
 * It is becasue the keys in references would have different character set.
 * 
 * 
 * This script could be useful also in case that you lost references 
 * when transfering database.
 * 
 * This script requires MySQL 5.0.2 and above because of the `SHOW TABLES FULL`
 * command (http://dev.mysql.com/doc/refman/5.0/en/show-tables.html). 
 * 
 * Adjust configuration of this script below this comment.
 * 
 * Written by Martin Minka, 2008
 */

//!!!!!!!!!!! USE ON YOUR OWN RISK !!!!!!!!!!!//

// change this lines to your needs
$connection = mysql_connect("localhost","root","");
$db_name = "???";
$character = "utf8";
$collate = "utf8_slovak_ci";
$output_path = "c:/";

// you don't need to be changed anything under this comment //
if ( !mysql_select_db($db_name) ) {
    echo "there is no database '$db_name'";
	return false;
}
//     
$fdrop = fopen($output_path . "fk_drop.sql", "w");
$fcreate = fopen($output_path . "fk_create.sql", "w");
$fchange = fopen($output_path . "convert.sql", "w");

// process structure
$res = mysql_query("SHOW FULL TABLES WHERE table_type='BASE TABLE'",$connection); //  like 'xxx'
while ( $r = mysql_fetch_row($res) ) {
    $res2 = mysql_query('show create table ' . $r[0],$connection);
    $r2 = mysql_fetch_row($res2);
    $tbl_name = $r2[0];   
    fwrite($fchange, "ALTER TABLE $tbl_name CONVERT TO CHARACTER SET $character COLLATE $collate;\n");                    
    $defs = explode("\n", $r2[1]);
    foreach ($defs as $s) {
        if ( strpos($s, 'CONSTRAINT ')!==FALSE 
            && strpos($s, ' FOREIGN KEY ')!==FALSE
            && strpos($s, ' REFERENCES ')!==FALSE
        ) {
            $l = strlen($s);
            if ($s[$l-1]==',') {
                $s = substr($s, 0, $l-1);
            }        
            $fk_name = explode("`", $s);
            $fk_name = $fk_name[1];
            fwrite($fdrop, "ALTER TABLE $tbl_name DROP FOREIGN KEY $fk_name;\n");
            fwrite($fcreate, "ALTER TABLE $tbl_name ADD$s;\n");
        }
    }
}

fclose($fdrop);
fclose($fcreate);
fclose($fchange);
