<?php

require_once '../diff-lib/Diff.php';

$o = "Привет\nэто\nтекст\nоригинал.\nА это\nудаленная строка\nбыла\nтолько что.";
$n = "Привет\nэто\nтекст\nизмененный.\nА это\nбыла\nи новая добавленная\nтолько что.";

$diff = Diff::compare($o, $n);

?>
<html>
<head>
    <title>Test 3</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../visualization/style.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="../visualization/script.js"></script>
</head>
<body>
    <div class="result">
        <div class="diff">
            <?php echo str_replace('"></p', '">&nbsp;</p', $diff); ?>
        </div>
    </div>
</body>
</html>
