<?php

namespace Valous\Capcha\Entity;

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
        $yaml = new Parser();
       
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
            $config[$configFilePath] = $yaml->parse(file_get_contents($configDir . '/' . $configFilePath));
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
