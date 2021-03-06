<?php

namespace Valous\Captcha\Entity;


/**
 * @author Valenta David
 */
class Image
{
    /** @var int */
    public $width;
    
    /** @var int */
    public $height;
    
    /** @var int[][] */
    public $backgroundColor;
    
    /** @var int[][][] */
    public $line;
}
