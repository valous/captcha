<?php

namespace Valous\Capcha\App;

use Valous\Capcha\Entity\Image;
use Valous\Capcha\Entity\Char;


/**
 * @author Valenta David
 */
class Creator
{
    /** @var ImageResources */
    private $image;


    /**
     * @param Image $image
     * @param Char[] $chars
     */
    public function create(Image $image, $chars)
    {
        $this->image = imagecreatetruecolor($image->width, $image->height);
        $this->setBackground($image);
        $this->generateText($chars);
             
        $string = '';
        foreach ($chars as $char) {
            $string .= $char->capchaChar;
        }
        
        $capchaHash = sha1(md5(sha1($string)));
        $name = time() . rand(0, 1000);

        $_SESSION['valous_capcha'] = $capchaHash;
        
        $capchaPath = __DIR__ . "/../../temp/capcha_$name.png";
        imagepng($this->image, $capchaPath);
        return "capcha_$name.png";
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
            $imagename = $this->colorsFont($char, ["width" => ($width / count($chars)), "height" => $height]);
            $image = imagecreatefrompng($imagename);
                       
            imagecopymerge($this->image, $image, $positionX, 0, 0, 0, $width, $height, 100);
            $positionX += $width / count($chars);
        }
    }
    
    
    /**
     * @param Char $char
     * @param int[] $size
     * @param int $countColor => 2 OR 4
     * @return Resources
     */
    private function colorsFont(Char $char, $size, $countColor = 4) 
    {
        $image = imagecreatetruecolor($size['width'], $size['height']);
        $image = $this->transparentImage($image);
        
        for ($i = 1; $i <= $countColor; $i++) {
            $this->cropImage($char, $image, $i, $countColor);
        }
        
        $imagename = __DIR__ . '/../../temp/char_' . time() . '_' . rand(0, 1000) . '_' . $char->capchaChar . '.png';
        
        imagepng($image, $imagename);
        imagedestroy($image);
        
        return $imagename;
    }
    
    
    /**
     * @param Char $char
     * @param Recources $image
     * @param int $actual
     * @param int $count
     * @return Recources
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
     * @return Resources
     */
    private function generateColorFont(Char $char, $size, $cropData) 
    {
        $image = imagecreatetruecolor($size['width'], $size['height']);
        $color = imagecolorallocate($image, $char->capchaColor['Red'][rand(0,1)], $char->capchaColor['Green'][rand(0,1)], $char->capchaColor['Blue'][rand(0,1)]);              
        
        $image = $this->transparentImage($image);
        
        $font =  __DIR__ . '/../../Resources/Fonts/' . $char->capchaFont;
        imagettftext($image, $char->capchaSize, $char->capchaAngle, 20, ($size['height'] / 2) + 20, $color, $font, $char->capchaChar);
        
        $img = imagecrop($image, $cropData);
        
        return $img;
    }
    
    
    /**
     * @param Recources $image
     * @return Recources
     */
    private function transparentImage($image) 
    {
        $black = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagecolortransparent($image, $black);
        imagesavealpha($image, false);
        
        return $image;
    }
}
