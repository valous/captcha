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
        return imagejpeg($this->image, __DIR__ . '/../../temp/capcha.jpg');
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
        $positionX = 20;
        
        foreach ($chars as $char) {
            $positionY = rand(($height / 2) + ($height / 4), ($height / 2) + ($height / 8));
            $font =  __DIR__ . '/../../Resources/Fonts/' . $char->capchaFont;
            $color = imagecolorallocate($this->image, $char->capchaColor['Red'], $char->capchaColor['Green'], $char->capchaColor['Blue']);
            imagettftext($this->image, $char->capchaSize, $char->capchaAngle, $positionX, $positionY, $color, $font, $char->capchaChar);
            
            $positionX += $width / count($chars);
        }
    }
}
