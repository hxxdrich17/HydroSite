<?php
$temp_fifo_file = '/tmp/dolphin-pipe-'.uniqid('dolph');
if (!posix_mkfifo($temp_fifo_file, 0600)) {
    echo "Fatal error: Cannot create fifo file: something wrong with the system.\n";
    exit(1);
}

function deleteTempFifo() { unlink($GLOBALS['temp_fifo']); }
register_shutdown_function('deleteTempFifo');

$cmdfp = fopen($temp_fifo_file, 'r+');
stream_set_blocking($cmdfp, 0);

putenv('TERM=vt100');
$cols = 80;
$rows = 24;

chdir(dirname(__FILE__));
$cmd = "bash --rcfile ./bashrc -i 2>&1";
// try to compile pseudo-terminal emulator if we can
if (!file_exists('pt')) {
    system('cc -D__'.strtoupper(trim(`uname`)).'__ -o pt pt.c -lutil 2>&1', $retval);
    if ($retval) echo('<b>Warning:</b> Cannot compile pseudotty helper');
}
clearstatcache();
if (file_exists('pt')) $cmd = "./pt $rows $cols $cmd";

$pp = proc_open($cmd, array(array('pipe','r'), array('pipe', 'w')), $pipes);
stream_set_blocking($pipes[0], 0);
stream_set_blocking($pipes[1], 0);
?>
<html><head><title>Terminal</title></head><body>
<script>
    var pipeName = <?=json_encode($temp_fifo_file)?>, pending_str = '', processing = false;
    var sendCmdInterv = setInterval(function() {
        if (processing) return;
        if (pending_str.length) {
            processing = true;
            var previous_str = pending_str;
            pending_str = '';
            var http = new XMLHttpRequest();
            http.open("GET", "send-cmd.php?pipe=" + pipeName + "&cmd=" + encodeURIComponent(previous_str), true);
            http.onreadystatechange = function() {
                if (http.readyState == 4 && http.status == 200) {
                    processing = false;
                    pending_str = '';
                } else {
                    pending_str = previous_str + pending_str;
                }
            };
            http.send(null);
        }
    }, 16);

    function send_cmd(val) {
        pending_str += val;
    }
</script>
<style>
    .term {
        font-family: monaco,courier,fixed,monospace,swiss,sans-serif;
        font-size: 13px;
        line-height: 16px;
        color: #f0f0f0;
        background: #000000;
    }

    tr {
        height: 16px;
    }

    .termReverse {
        color: #000000;
        background: #00ff00;
    }
</style>
<script src="http://bellard.org/jslinux/utils.js"></script>
<script src="http://bellard.org/jslinux/term.js"></script>
<script>var term = new Term(<?=$cols?>, <?=$rows?>, send_cmd); term.open();</script>
<?php
echo "<!-- ".str_repeat('-', 4096)." -->\n";
flush();

while (!feof($pipes[1])) {
	$ln = fgets($pipes[1], 4096);
	if ($ln !== false) {
        $ln = str_replace("\n", "\r\n", $ln);
		echo '<script>term.write('.json_encode($ln).');</script>';
        flush();
		continue;
	}
	
    $inp_ln = fgets($cmdfp, 4096);
    if ($inp_ln !== false) {
        // ensure that command is fully written by setting blocking to 1
        stream_set_blocking($pipes[0], 1);
        fwrite($pipes[0], $inp_ln);
        stream_set_blocking($pipes[0], 0);
    }
	
	usleep(20000);
}

proc_close($pp);
?>
<script>clearInterval(sendCmdInterv);</script>
</body>
</html>
