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
        
        // Následující část kódu je provizorní, bude změněna!
        // ---------->
        if ($countColor == 2) {
            imagecopy($image, $this->generateColorFont($char, $size, ['x' => 0, 'y' => 0, 'width' => $size['width'], 'height' => ($size['height']/2)]), 0, 0, 0, 0, $size['width'], ($size['height'] / 2));
            imagecopy($image, $this->generateColorFont($char, $size, ['x' => 0, 'y' => ($size['height']/2), 'width' => $size['width'], 'height' => ($size['height']/2)]), 0, ($size['height']/2)-1, 0, 0, $size['width'], ($size['height'] / 2));
        } elseif ($countColor == 4) {
            imagecopy($image, $this->generateColorFont($char, $size, ['x' => 0, 'y' => 0, 'width' => ($size['width'] / 2), 'height' => ($size['height']/2)+1]), 0, 0, 0, 0, $size['width']/2, ($size['height'] / 2));
            imagecopy($image, $this->generateColorFont($char, $size, ['x' => 0, 'y' => ($size['height']/2), 'width' => ($size['width'] / 2), 'height' => ($size['height']/2)+1]), 0, ($size['height']/2), 0, 0, $size['width']/2, ($size['height'] / 2));
            imagecopy($image, $this->generateColorFont($char, $size, ['x' => ($size['width'] / 2), 'y' => 0, 'width' => ($size['width'] / 2), 'height' => ($size['height']/2)+2]), $size['width']/2, 0, 0, 0, $size['width']/2, ($size['height'] / 2));
            imagecopy($image, $this->generateColorFont($char, $size, ['x' => ($size['width'] / 2), 'y' => ($size['height']/2), 'width' => ($size['width'] / 2), 'height' => ($size['height']/2)+2]), $size['width']/2, ($size['height']/2), 0, 0, $size['width']/2, ($size['height'] / 2));
        }
        // <----------
        
        $imagename = __DIR__ . '/../../temp/char_' . time() . rand(0, 1000) . $char->capchaChar . '.png';
        
        imagepng($image, $imagename);
        imagedestroy($image);
        
        return $imagename;
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
