<html>
<head>
    <title>Diff</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <div class="wrap">
        <h1><a href="">Diff</a></h1>
        <div class="data">
            <form class="form" action="" method="post">
                <div class="box item_1">
                    <h2>Оригинал текста <span>(original)</span></h2>
                    <textarea name="text1" cols="30" rows="10"><?php echo (isset($_POST['text1'])) ? $_POST['text1'] : ''; ?></textarea>
                </div>
                <div class="box item_2">
                    <h2>Измененный текст <span>(new version)</span></h2>
                    <textarea name="text2" cols="30" rows="10"><?php echo (isset($_POST['text2'])) ? $_POST['text2'] : ''; ?></textarea>
                </div>
                <div class="button">
                    <input type="submit" value="Сравнить">
                </div>
            </form>
        </div>
        <div class="result">
            <h2>Изменения измененного текста относительно оригинального</h2>
            <div class="diff">
                <!-- пример отображаемых вариантов строк
                <p class="mod" data-storage="Старое предложение">Измененное предложение</p>
                <p class="ins">Новое предложение</p>
                <p class="del">Удаленное предложение</p>
                <p class="nochange">Текст без изменений</p> -->
                <?php
                    if (isset($_POST['text1']) && isset($_POST['text2']))
                    {
                        if (empty($_POST['text1']) && empty($_POST['text2']))
                        {
                            echo "Оба поля пустых. Заполните хотя бы одно из полей.";
                        }
                        else
                        {
                            $o = $_POST['text1'];
                            $n = $_POST['text2'];
                            require_once '../diff-lib/Diff.php';
                            $diff = Diff::compare($o, $n);
                            // это замена для отображения, в браузер передается все правильно, но пустую строку браузер схлопывает
                            $diff = str_replace('"></p', '">&nbsp;</p', $diff);
                            echo $diff;
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</body>
</html>