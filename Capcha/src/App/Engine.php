<?php

namespace Valous\Capcha\App;

use Valous\Capcha\Entity\Char;
use Valous\Capcha\Config\Config;
use Valous\Capcha\Entity\Image;
use Valous\Capcha\App\Creator;


/**
 * @author Valenta David
 */
class Engine
{
    /** @var Char[] */
    private $capchaString;
    
    /** @var array */
    private $config;
    
    /** @var Creator */
    private $capchaImage;
    
    /**
     * @param string $configDir
     */
    public function __construct($configDir)
    {
        $config = new Config($configDir);
        $this->config = $config->getConfig();
    }

    
    /**
     */
    public function createCapcha()
    {       
        $image = new Image();
        $image->height = $this->config['config.yml']['Size']['Height'];
        $image->width = $this->config['config.yml']['Size']['Width'];
        $image->backgroundColor = $this->config['config.yml']['ColorsBackground'];
        
        $this->generateChars();
        
        $this->capchaImage = new Creator();
        $image = $this->capchaImage->create($image, $this->capchaString);
    }
    
    
    private function generateChars()
    {
        $chars = $this->config['chars.yml'];
        $fonts = $this->config['fonts.yml'];
        
        $fontColor = $this->config['config.yml']['ColorsFont'];
        
        for ($i = 0; $i < $this->config['config.yml']['Lenght']; $i++) {
            $char = new Char();
            $char->capchaChar = $chars[rand(0, (count($chars)) - 1)];
            $char->capchaColor = [
                'Red' => rand($fontColor['Red']['Min'], $fontColor['Red']['Max']),
                'Green' => rand($fontColor['Green']['Min'], $fontColor['Green']['Max']),
                'Blue' => rand($fontColor['Blue']['Min'], $fontColor['Blue']['Max']),
            ];
            $char->capchaFont = $fonts[rand(0, (count($fonts)) - 1)];
            $char->capchaSize = rand($this->config['config.yml']['SizeChar']['Min'], $this->config['config.yml']['SizeChar']['Max']);
            $char->capchaAngle = rand($this->config['config.yml']['AngleChar']['Min'], $this->config['config.yml']['AngleChar']['Max']);
            
            $this->capchaString[] = $char;
        }
    }
}
