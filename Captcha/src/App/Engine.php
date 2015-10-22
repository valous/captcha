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
    /** @var array */
    private $config;

    /** @var resource */
    private $captcha;

    
    /**
     * @param string $configDir
     */
    public function __construct($configDir)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $config = new Config($configDir);
        $this->config = $config->getConfig();
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
        
        $captchaChars = $this->generateChars();
        
        $captchaImage = new Creator();
        $this->captcha = $captchaImage->create($image, $captchaChars);
    }


    /**
     */
    public function render()
    {
        header('Content-Type: image/png');
        imagepng($this->captcha);
        imagedestroy($this->captcha);
    }


    /**
     * @param string $fileName
     */
    public function save($fileName)
    {
        imagepng($this->captcha, $fileName);
        imagedestroy($this->captcha);
    }


    /**
     * @param string $postCaptcha
     * @return bool
     */
    public function checkCaptcha($postCaptcha) 
    {
        if (sha1(strtolower($postCaptcha)) === $_SESSION['valous_captcha']) {
            return true;
        }
        
        return false;
    }


    /**
     * @return Char[]
     */
    private function generateChars()
    {
        $chars = $this->config['chars.yml'];
        $fonts = $this->config['fonts.yml'];
        
        $fontColor = $this->config['config.yml']['ColorsFont'];

        $captchaString = [];
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
            
            $captchaString[] = $char;
        }

        return $captchaString;
    }
}
