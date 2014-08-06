<?php

namespace Valous\Capcha\App;

use Valous\Capcha\Entity\Char;
use Valous\Capcha\Entity\Config;

/**
 * @author Valenta David
 */
class Engine
{
    /** @var Char[] */
    private $capchaString;
    
    /** @var Config */
    private $config;
    
    /**
     * @param string $configDir
     */
    public function __construct($configDir) {
        $this->config = new Config($configDir);
        
        var_dump($this->config->getConfig());
    }
}
