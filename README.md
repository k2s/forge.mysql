My forge.mysql.com contributions
================================

WorkDayDiff.sql
---------------

I created this function to calculate "working day" difference of two dates. If you have table with list of holidays you may uncomment part in this function to exclude days of holidays also.

RoundTo0_5.sql
--------------

I use this function to round currencies where there are coins only to half of the main currency. For example there are only 50 haler coins of Slovak koruna. (http://en.wikipedia.org/wiki/Slovak_koruna)

ConvertTableCharset.php
-----------------------

Drops references, converts charset and recreates references.

This script will generate 3 files (fk_drop.sql, fk_create.sql, convert.sql) into $output_path. It will not modify your database when you run it. You have to manualy execute the generated scripts to your database in this order: 1. fk_drop.sql - will drop all references between tables 2. convert.sql - (optional) will convert tables to defined char. set 3. fk_create.sql - this will create the references back
The reason for this script is that if you use (var)char fields in references and you need to change character set or colation you will experience error: `Error Code : 1025 Error on rename of '...' to '...' (errno: 150)` It is becasue the keys in references would have different character set.
This script could be useful also in case that you lost references when transfering database.
This script requires MySQL 5.0.2 and above because of the `SHOW TABLES FULL` command (http://dev.mysql.com/doc/refman/5.0/en/show-tables.html).

License
-------

all code in this repository is free and unencumbered public domain software. For more information, see http://unlicense.org/ or the accompanying UNLICENSE file.