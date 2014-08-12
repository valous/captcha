<?php

namespace Valous\Capcha\Entity;


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
