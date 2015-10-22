<?php

namespace Valous\Captcha\App;

use Valous\Captcha\Entity\Char;
use Valous\Captcha\Config\Config;
use Valous\Captcha\Entity\Image;


/**
 * @author Valenta David
 */
class Engine
{
    /** @var Char[] */
    private $captchaString;
    
    /** @var array */
    private $config;
    
    /** @var Creator */
    private $captchaImage;
    
    /** @var string */
    private $tempDir;
    
    /**
     * @param string $configDir
     * @param string $tempDir
     */
    public function __construct($configDir, $tempDir)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $config = new Config($configDir);
        $this->config = $config->getConfig();
        $this->tempDir = $tempDir;
        
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
        
        $this->captchaImage = new Creator($this->tempDir);
        $captchaName = $this->captchaImage->create($image, $this->captchaString);
        
        return $captchaName;
    }
    
    
    /**
     * @param string $postCaptcha
     * @return bool
     */
    public function checkCaptcha($postCaptcha) 
    {
        if (sha1(md5(sha1(strtolower($postCaptcha)))) === $_SESSION['valous_captcha']) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * @param bool $all
     */
    public function cleanTemp($all = false)
    {
        $dirHandle = opendir($this->tempDir);

        $files = [];
        while ($data = readdir($dirHandle)) {
            $files[] = $data;
        }   
        
        $files = array_diff($files, ['.', '..']);
        
        foreach ($files as $file) {
            $filename = $this->tempDir . $file;
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
            $char->captchaChar = $chars[rand(0, (count($chars)) - 1)];
            $char->captchaColor = [
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
            $char->captchaFont = $fonts[rand(0, (count($fonts)) - 1)];
            $char->captchaSize = rand($this->config['config.yml']['SizeChar']['Min'], $this->config['config.yml']['SizeChar']['Max']);
            $char->captchaAngle = rand($this->config['config.yml']['AngleChar']['Min'], $this->config['config.yml']['AngleChar']['Max']);
            
            $this->captchaString[] = $char;
        }
    }
}
