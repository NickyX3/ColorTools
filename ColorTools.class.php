<?php

/**
 * ColorTools Class. Generate Colors Palletes etc
 * @author Nic Latyshev
 * @version 0.1
 * nickyx3@gmail.com
 */
class ColorTools {
	
	/**
	 * Generate List of colors on Hue Ring based on some selected color
	 * @param 	string 	$pNbColors	Number of colors
	 * @param 	string 	$baseRGBHex	Base HexRGB color ( 428bca for example )
	 * @param 	boolean $cleanRGB	Return colors in HeaxRGB format like HTML #ff0000
	 * @param	boolean	$usehexkey	Use 2-digits hex index in array keys
	 * @return 	array	List of result palete colors
	 */
	public static function ColorArrayGenerator ($pNbColors='2',$baseRGBHex='428bca',$cleanRGB=false,$usehexkey=false) {
		$colors = array();
		$baseRGB = self::rgbHexToRGB($baseRGBHex);
		$baseHSL = array();
	
		$baseHSL = self::rgbToHsl($baseRGB);
		
		$currentHue = $baseHSL['H'];
		
		$key = sprintf( '%02s',dechex(0) );
		
		if ( $cleanRGB ) {
			if ( $usehexkey === true ) {
				$colors[$key] = self::RGBTorgbHex($baseRGB,0);
			} else {
				$colors[] = self::RGBTorgbHex($baseRGB,0);
			}
		} else {
			if ( $usehexkey === true ) {
				$colors[$key] = self::RGBTorgbHex($baseRGB,1);
			} else {
				$colors[] = self::RGBTorgbHex($baseRGB,1);
			}
		}
	
		$step = 360/$pNbColors;
		$nextHSL = '';
		$nextRGB = '';
	
		for ($i = 1; $i < $pNbColors; $i++) {
			$currentHue = $currentHue + $step;
			if ($currentHue > 360)
			{
				$currentHue = $currentHue - 360;
			}
	
			$nextHSL = array('H'=>$currentHue, 'S'=>$baseHSL['S'], 'L'=>$baseHSL['L']);
			$nextRGB = self::hslToRgb($nextHSL);
			
			if ( $cleanRGB ) {
				if ( $usehexkey === true ) {
					$key = sprintf( '%02s',dechex($i) );
					$colors[$key] = self::RGBTorgbHex($nextRGB,0);
				} else {
					$colors[] = self::RGBTorgbHex($nextRGB,0);
				}
			} else {
				if ( $usehexkey === true ) {
					$key = sprintf( '%02s',dechex($i) );
					$colors[$key] = self::RGBTorgbHex($nextRGB,1);
				} else {
					$colors[] = self::RGBTorgbHex($nextRGB,1);
				}
			}
		}
		
		return $colors;
	}
	
	/**
	 * Convert function from rgbHEX (RRGGBB) to array with 
	 * decimal RGB values like array('R'=>0..255,'G'=>0..255,'B'=>0..255)
	 * @param 	string 	$rgbhex
	 * @return 	array
	 */
	public static function rgbHexToRGB ($rgbhex) {
		$a = str_split($rgbhex,2);
		return array( 'R'=>hexdec($a[0]),'G'=>hexdec($a[1]),'B'=>hexdec($a[2]) );
	}
	
	/**
	 * Convert function color RGB array like array('R'=>0..255,'G'=>0..255,'B'=>0..255)
	 * to rgbHEX format like #RRGGBB or with secont param is "true"
	 * 0xRRGGBB converting to decimal integer (use this color format in ChartDirector )
	 * @param 	array 	$rgb
	 * @param 	string 	$dec
	 * @return 	number|string
	 */
	public static function RGBTorgbHex ($rgb=array(),$dec=false) {
		$color = sprintf( '%02s',dechex($rgb['R'])).sprintf( '%02s',dechex($rgb['G'])).sprintf( '%02s',dechex($rgb['B']));
		if ( $dec ) {
			return hexdec($color);
		} else {
			return '#'.$color;
		}
	}
	
	/**
	 * Convert function color RGB array like array('R'=>0..255,'G'=>0..255,'B'=>0..255)
	 * to HSL array like array('H'=>0..255,'S'=>0..255,'L'=>0..255)
	 * @param 	array 	$rgb
	 * @return 	array
	 */
	public static function rgbToHsl( $rgb=array() ) {
		
		$r = $rgb['R'];
		$g = $rgb['G'];
		$b = $rgb['B'];
		
		$oldR = $r;
		$oldG = $g;
		$oldB = $b;
	
		$r /= 255;
		$g /= 255;
		$b /= 255;
	
		$max = max( $r, $g, $b );
		$min = min( $r, $g, $b );
	
		$h = 0;
		$s = 0;
		$l = ( $max + $min ) / 2;
		$d = $max - $min;
	
		if( $d == 0 ){
			$h = $s = 0; // achromatic
		} else {
			$s = $d / ( 1 - abs( 2 * $l - 1 ) );
	
			switch( $max ){
				case $r:
					$h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
					if ($b > $g) {
						$h += 360;
					}
					break;
	
				case $g:
					$h = 60 * ( ( $b - $r ) / $d + 2 );
					break;
	
				case $b:
					$h = 60 * ( ( $r - $g ) / $d + 4 );
					break;
			}
		}
	
		return array( 'H'=>round( $h, 2 ), 'S'=>round( $s, 2 ), 'L'=>round( $l, 2 ) );
	}
	
	/**
	 * Convert function color HSL array like array('H'=>0..255,'S'=>0..255,'L'=>0..255)
	 * to color RGB array like array('R'=>0..255,'G'=>0..255,'B'=>0..255)
	 * @param 	array 	$hsl
	 * @return 	array
	 */
	public static function hslToRgb( $hsl=array() ) {
		
		$h = $hsl['H'];
		$s = $hsl['S'];
		$l = $hsl['L'];
		
		$r = 0;
		$g = 0;
		$b = 0;
	
		$c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
		$x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
		$m = $l - ( $c / 2 );
	
		if ( $h < 60 ) {
			$r = $c;
			$g = $x;
			$b = 0;
		} else if ( $h < 120 ) {
			$r = $x;
			$g = $c;
			$b = 0;
		} else if ( $h < 180 ) {
			$r = 0;
			$g = $c;
			$b = $x;
		} else if ( $h < 240 ) {
			$r = 0;
			$g = $x;
			$b = $c;
		} else if ( $h < 300 ) {
			$r = $x;
			$g = 0;
			$b = $c;
		} else {
			$r = $c;
			$g = 0;
			$b = $x;
		}
	
		$r = ( $r + $m ) * 255;
		$g = ( $g + $m ) * 255;
		$b = ( $b + $m  ) * 255;
	
		return array( 'R'=>floor( $r ), 'G'=>floor( $g ), 'B'=>floor( $b ) );
	}
	
	/**
	 * Function return black or white for input some #hexRRGGBB color based on color lightness
	 * @param 	string	$color
	 * @return 	string
	 */
	public static function getContrastColor ( $color ) {
		$color	 = str_replace('#', '', $color);
		$baseRGB = self::rgbHexToRGB($color);
		$baseHSL = array();
		
		$baseHSL = self::rgbToHsl($baseRGB);
		
		$L = $baseHSL['L']*100;
		if ( $L <= 70 ) {
			return '#ffffff';
		} else {
			return '#000000';
		}
	}
}

?>