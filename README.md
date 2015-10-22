# Captcha
## Vygenerování

Vygenerování captchy pomoci php.

```php
use Valous\Captcha\App\Engine;

/**
 * $configDir - cesta ke složce s konfiguračními soubory
 */
$engine = new Engine($configDir);
$engine->createCaptcha(); // Vytvoření captchy

/**
 * Vykreslení captchy na obrazovku
 */
$engine->render();

/**
 * Uložení captchy do souboru
 * $fileName - cesta k souboru do kterého má být captcha uložena
 */
$engine->save($fileName);
```

## Validace
Validace Captchy odeslane ve formuláři
```php
use Valous\Captcha\App\Engine;

/**
 * $configDir cesta ke složce s konfiguračními soubory
 */
$engine = new Engine($configDir);

/**
 * $validate - Vyhodnocení validace (true / false)
 */
$validate = $engine->checkCaptcha($_POST['valous_capcha']);
```

## Použití

### Soubor captcha.php

```php
use Valous\Captcha\App\Engine;

$engine = new Engine($configDir);
$engine->createCaptcha(); // Vytvoření captchy
$engine->render();
```

### Soubor index.html

```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>         
    <body>
        <img src="captcha.php" alt="Captcha">        
        <form method="POST" action="validace.php">
            <input type="text" name="valous_capcha">
            <input type="submit" value="Odešli!">
        </form>
    </body>
</html>
```

### Soubor validace.php
```php
use Valous\Captcha\App\Engine;

$engine = new Engine($configDir);

$validate = $engine->checkCaptcha($_POST['valous_capcha']);

if ($validate) {
    // Captcha opsaná správně
} else {
    // Captcha opsaná špatně
}

```
