<?php

namespace Valous\Captcha\Entity;


/**
 * @author Valenta David
 */
class Char
{
    /** @var string */
    public $captchaChar;
    
    /** @var string */
    public $captchaFont;
    
    /** @var int */
    public $captchaSize;
    
    /** @var int */
    public $captchaAngle;
    
    /** @var int[] */
    public $captchaColor;
}
