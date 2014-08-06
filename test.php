<?php

require __DIR__ . '/vendor/autoload.php';

use Valous\Capcha\App\Engine;

$configDir = __DIR__ . "/Capcha/Resources/Config";
$engine = new Engine($configDir);
