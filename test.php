<?php

require __DIR__ . '/vendor/autoload.php';

use Valous\Captcha\App\Engine;

$configDir = __DIR__ . "/Captcha/Resources/Config";
$tempDir = __DIR__ . "/temp/";

$engine = new Engine($configDir, $tempDir);

if (isset($_POST['valous_capcha'])) {
    $validate = $engine->checkCaptcha($_POST['valous_capcha']);
}

$capchaName = $engine->createCaptcha();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>         
    <body>
        <img src="./temp/<?php echo $capchaName; ?>">

        <?php
            if (isset($validate)) {
                if ($validate) {
                    echo 'Zvládl si to!';
                } else {
                    echo 'Zadaná kombinace neodpovídá znakům na obrázku!';
                }
            }
        ?>
        
        <form method="POST" action="">
            <input type="text" name="valous_capcha">
            <input type="submit" value="Odešli!">
        </form>
    </body>
</html>
