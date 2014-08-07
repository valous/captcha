<?php

require __DIR__ . '/vendor/autoload.php';

use Valous\Capcha\App\Engine;

$configDir = __DIR__ . "/Capcha/Resources/Config";
$engine = new Engine($configDir);

if (isset($_POST['valous_capcha'])) {
    $validate = $engine->checkCapcha($_POST['valous_capcha']);
}

$capchaName = $engine->createCapcha();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>         
    <body>
        <img src="./Capcha/temp/<?php echo $capchaName; ?>">

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

