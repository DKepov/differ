<?php

require_once '../diff-lib/Diff.php';

$o = file_get_contents('../../task/test_2/Text-B1.txt');
$n = file_get_contents('../../task/test_2/Text-B2.txt');

$diff = Diff::compare($o, $n);

?>
<html>
<head>
    <title>Test 2</title>
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
