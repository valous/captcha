<?php

namespace Valous\Captcha\App;

use Valous\Captcha\Entity\Image;
use Valous\Captcha\Entity\Char;


/**
 * @author Valenta David
 */
class Creator
{
    /** @var resource */
    private $image;

    /** @var string */
    private $tempDir;
    
    
    /**
     * @param string $tempDir
     */
    public function __construct($tempDir) {
        $this->tempDir = $tempDir;
    }
    

    /**
     * @param Image $image
     * @param Char[] $chars
     * @return string
     */
    public function create(Image $image, array $chars)
    {
        $this->image = imagecreatetruecolor($image->width, $image->height);
        $this->setBackground($image);
        $this->generateText($chars);
        $this->drawLine($image->line);   
        
        $string = '';
        foreach ($chars as $char) {
            $string .= $char->captchaChar;
        }
        
        $captchaHash = sha1(md5(sha1($string)));
        $name = time() . rand(0, 1000);

        $_SESSION['valous_captcha'] = $captchaHash;
        
        $captchaPath = "$this->tempDir/captcha_$name.png";
        imagepng($this->image, $captchaPath);
        
        return "captcha_$name.png";
    }
    
    
    /**
     * @param Image $image
     */
    private function setBackground(Image $image) 
    {   
        for ($positionX = 0; $positionX <= $image->width; $positionX++) {
            for ($positionY = 0; $positionY <= $image->height; $positionY++) {
                $color = imagecolorallocate($this->image, rand($image->backgroundColor['Red']['Min'], $image->backgroundColor['Red']['Max']), rand($image->backgroundColor['Green']['Min'], $image->backgroundColor['Green']['Max']), rand($image->backgroundColor['Blue']['Min'], $image->backgroundColor['Blue']['Max']));
                imagesetpixel($this->image, $positionX, $positionY, $color);
            }
        }
    }
    
    
    /**
     * @param Char[] $chars
     */
    private function generateText($chars) 
    {
        $width = imagesx($this->image);
        $height = imagesy($this->image);       
        $positionX = 0;
        
        foreach ($chars as $char) {
            $imageName = $this->colorsFont($char, ["width" => ($width / count($chars)), "height" => $height]);
            $image = imagecreatefrompng($imageName);
                       
            imagecopymerge($this->image, $image, $positionX, 0, 0, 0, $width, $height, 100);
            $positionX += $width / count($chars);
        }
    }
    
    
    /**
     * @param Char $char
     * @param int[] $size
     * @param bool $countColor
     * @return resource
     */
    private function colorsFont(Char $char, $size, $countColor = true) 
    {
        $image = imagecreatetruecolor($size['width'], $size['height']);
        $image = $this->transparentImage($image);
        $countColor = ($countColor)?4:2;        
        
        for ($i = 1; $i <= $countColor; $i++) {
            $this->cropImage($char, $image, $i, $countColor);
        }
        
        $imageName = $this->tempDir . 'char_' . time() . '_' . rand(0, 1000) . '_' . $char->captchaChar . '.png';
        
        imagepng($image, $imageName);
        imagedestroy($image);
        
        return $imageName;
    }
    
    
    /**
     * @param Char $char
     * @param resource $image
     * @param int $actual
     * @param int $count
     * @return resource
     */
    private function cropImage(Char $char, $image, $actual, $count) 
    {
        $width = imagesx($image) / ($count / 2);
        $height = imagesy($image) / ($count / 2);
        $x = (($count - $actual) > ($count / 2)) ? ($actual - 1) * $width : abs($count - $actual - 1) * $width;
        $y = (($count - $actual) >= ($count / 2)) ? 0 : $height;
        $size = ['width' => imagesx($image), 'height' => imagesy($image)];
        
        $cropImage = $this->generateColorFont($char, $size, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => ($height + 1)]);
        imagecopy($image, $cropImage, $x, $y, 0, 0, $width, $height);
        
        return $image;
    }
    
    
    /**
     * @param Char $char
     * @param array $size
     * @param array $cropData
     * @return resource
     */
    private function generateColorFont(Char $char, $size, $cropData) 
    {
        $image = imagecreatetruecolor($size['width'], $size['height']);
        $color = imagecolorallocate($image, $char->captchaColor['Red'][rand(0,1)], $char->captchaColor['Green'][rand(0,1)], $char->captchaColor['Blue'][rand(0,1)]);              
        
        $image = $this->transparentImage($image);
        
        $font =  __DIR__ . '/../../Resources/Fonts/' . $char->captchaFont;
        imagettftext($image, $char->captchaSize, $char->captchaAngle, 20, ($size['height'] / 2) + 20, $color, $font, $char->captchaChar);
        
        $img = imagecrop($image, $cropData);
        
        return $img;
    }
    
    
    /**
     * @param resource $image
     * @return resource
     */
    private function transparentImage($image) 
    {
        $black = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagecolortransparent($image, $black);
        imagesavealpha($image, false);
        
        return $image;
    }
    
    
    /**
     * @var int[][][] $config
     */
    private function drawLine($config)
    {
        $count = rand($config['Count']['Min'], $config['Count']['Max']);
        $width = imagesx($this->image);
        $height = imagesy($this->image);
        
        for ($i = 0; $i < $count; $i++) {
            $size = rand($config['Size']['Min'], $config['Size']['Max']);
            $x1 = rand(0, $width);
            $x2 = rand(0, $width);
            $y1 = rand(0, $height);
            $y2 = rand(0, $height);
            $color = imagecolorallocate($this->image,
                    rand($config['Color']['Red']['Min'], $config['Color']['Red']['Max']),
                    rand($config['Color']['Green']['Min'], $config['Color']['Green']['Max']),
                    rand($config['Color']['Blue']['Min'], $config['Color']['Blue']['Max'])
            );
            
            for($o = 0; $o < $size; $o++) {
                imageline($this->image, $x1, $y1 + $o, $x2, $y2 + $o, $color);
            }
        }
    }
}
