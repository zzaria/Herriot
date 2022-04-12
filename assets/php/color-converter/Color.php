<?php

include("../../assets/php/color-converter/Colors/Hsl.php");
include("../../assets/php/color-converter/Colors/Named.php");
include("../../assets/php/color-converter/Colors/Rgb.php");
include("../../assets/php/color-converter/Colors/Hex.php");
include("../../assets/php/color-converter/Colors/Rgba.php");

class Color 
{

    /** @var int */
    protected $red;
    
    /** @var int */
    protected $green;
    
    /** @var int */
    protected $blue;

    /** @var float */
    protected $alpha = 1.0;

    public function getRed(): int
    {
        return $this->red;
    }

    public function getGreen(): int
    {
        return $this->green;
    }

    public function getBlue(): int
    {
        return $this->blue;
    }

    public function getAlpha(): float
    {
        return $this->alpha;
    }

    public function setRed(int $value)
    {
        $this->red = $value;
        return $this;
    }

    public function setGreen(int $value)
    {
        $this->green = $value;
        return $this;
    }

    public function setBlue(int $value)
    {
        $this->blue = $value;
        return $this;
    }

    public function setAlpha(float $value)
    {
        $this->alpha = $value;
        return $this;
    }

    public static function fromString($colorString)
    {
        $colorString = trim($colorString);
        $classes = [
            Rgb::class,
            Hex::class,
            Rgba::class,
            Named::class,
            Hsl::class,
        ];

        foreach ($classes as $class) {
            try {
                return $class::fromString($colorString);
            }
            catch (\Exception $ex) {
                // skip this, we try to find a working method and raise an exception if nothing works
            }
        }

        throw new \Exception('Unsupported color string ' . $colorString);
    }

    protected function rgbValueToHex(int $value)
    {
        return str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
    }

    public function toHex()
    {
        return '#' .
            $this->rgbValueToHex($this->red) .
            $this->rgbValueToHex($this->green) .
            $this->rgbValueToHex($this->blue);
    }

    public function toRgb()
    {
        return "rgb({$this->red}, {$this->green}, {$this->blue})";
    }

    public function toRgba()
    {
        return "rgba({$this->red}, {$this->green}, {$this->blue}, $this->alpha)";
    }
    public function toHSL ()
    {                                 
       $HSL = array(); 

       $var_R = ($this->red / 255); 
       $var_G = ($this->green / 255); 
       $var_B = ($this->blue / 255); 

       $var_Min = min($var_R, $var_G, $var_B); 
       $var_Max = max($var_R, $var_G, $var_B); 
       $del_Max = $var_Max - $var_Min; 

       $V = $var_Max; 

       if ($del_Max == 0) 
       { 
          $H = 0; 
          $S = 0; 
       } 
       else 
       { 
          $S = $del_Max / $var_Max; 

          $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
          $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
          $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 

          if      ($var_R == $var_Max) $H = $del_B - $del_G; 
          else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B; 
          else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R; 

          if ($H<0) $H++; 
          if ($H>1) $H--; 
       } 
       $L=$V*(1-$S/2);
       $S=$L==0||$L==1? 0: ($V-$L)/min($L,1-$L);
       $HSL['H'] = $H; 
       $HSL['S'] = $S; 
       $HSL['L'] = $L; 

       return $HSL; 
    } 
}