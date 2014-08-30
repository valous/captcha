<?php

namespace Valous\Captcha\App;

use Valous\Captcha\Entity\Char;
use Valous\Captcha\Config\Config;
use Valous\Captcha\Entity\Image;
use Valous\Captcha\App\Creator;


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
        session_start();
        $config = new Config($configDir);
        $this->config = $config->getConfig();
        
        $this->cleanTemp();
    }

    
    /**
     * @return string
     */
    public function createCaptcha()
    {       
        $image = new Image();
        $image->height = $this->config['config.yml']['Size']['Height'];
        $image->width = $this->config['config.yml']['Size']['Width'];
        $image->backgroundColor = $this->config['config.yml']['ColorsBackground'];
        $image->line = $this->config['config.yml']['Line'];
        
        $this->generateChars();
        
        $this->capchaImage = new Creator();
        $capchaName = $this->capchaImage->create($image, $this->capchaString);
        
        return $capchaName;
    }
    
    
    /**
     * @param string $postCaptcha
     * @return bool
     */
    public function checkCaptcha($postCaptcha) 
    {
        if (sha1(md5(sha1(strtolower($postCaptcha)))) === $_SESSION['valous_capcha']) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * @param bool $all
     */
    public function cleanTemp($all = false)
    {
        $tempDir = __DIR__ . '/../../temp/';
        $dirHandle = opendir($tempDir);

        while ($data = readdir($dirHandle)) {
            $files[] = $data;
        }   
        
        $files = array_diff($files, ['.', '..']);
        
        foreach ($files as $file) {
            $filename = $tempDir . $file;
            if (!$all && filemtime($filename) < (time() - 4)) {
                unlink($filename);
            } elseif ($all) {
                unlink($filename); 
            }
        }
    }
    

    /**
     */
    private function generateChars()
    {
        $chars = $this->config['chars.yml'];
        $fonts = $this->config['fonts.yml'];
        
        $fontColor = $this->config['config.yml']['ColorsFont'];
        
        for ($i = 0; $i < $this->config['config.yml']['Lenght']; $i++) {
            $char = new Char();
            $char->capchaChar = $chars[rand(0, (count($chars)) - 1)];
            $char->capchaColor = [
                'Red' => [
                    rand($fontColor['Red']['Min'], $fontColor['Red']['Max']),
                    rand($fontColor['Red']['Min'], $fontColor['Red']['Max'])
                ],
                'Green' => [
                     rand($fontColor['Green']['Min'], $fontColor['Green']['Max']),
                     rand($fontColor['Green']['Min'], $fontColor['Green']['Max']),
                ],
                'Blue' => [
                    rand($fontColor['Blue']['Min'], $fontColor['Blue']['Max']),
                    rand($fontColor['Blue']['Min'], $fontColor['Blue']['Max']),
                ]
            ];
            $char->capchaFont = $fonts[rand(0, (count($fonts)) - 1)];
            $char->capchaSize = rand($this->config['config.yml']['SizeChar']['Min'], $this->config['config.yml']['SizeChar']['Max']);
            $char->capchaAngle = rand($this->config['config.yml']['AngleChar']['Min'], $this->config['config.yml']['AngleChar']['Max']);
            
            $this->capchaString[] = $char;
        }
    }
}
