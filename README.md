# Captcha
## Vygenerování
Vygenerování captchy pomoci php
```php
use Valous\Captcha\App\Engine;

/**
 * $configDir cesta ke složce s konfiguračními soubory
 * $tempDir cesta ke složce pro uložení cache (musí byt prázdná, nesmí obsahovat jiné soubory!)
 */
$engine = new Engine($configDir, $tempDir);
$capchaName = $engine->createCaptcha(); // Vytvoření captchy a získání názvu souboru.
```

## Validace
Validace Captchy odeslane ve formuláři
```php
$validate = $engine->checkCaptcha($_POST['valous_capcha']);
```
