<?php

namespace Valous\Captcha\Config;

use Symfony\Component\Yaml\Parser;


/**
 * @author Valenta David
 */
class Config
{
    /** @var array */
    private $config;

    
    /**
     * @param string $configDir
     */
    public function __construct($configDir)
    {
        $yml = new Parser();

        $configFilesPath = [];
        if (is_dir($configDir)) {
            $dirHandler = openDir($configDir);

            while ($data = readdir($dirHandler)) {
                $configFilesPath[] = $data;
            }
            
            $configFilesPath = array_diff($configFilesPath, ['.', '..']);
            closedir();
        }
        
        $config = [];
        foreach ($configFilesPath as $configFilePath) {
            $config[$configFilePath] = $yml->parse(file_get_contents($configDir . '/' . $configFilePath));
        }
        
        $this->config = $config;
    }
    
    
    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
