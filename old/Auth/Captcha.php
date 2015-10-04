<?php

namespace Auth;

return new Captcha;

/**
 * Captcha system
 */
class Captcha
{
	/**
	 * Configuration array
	 * @var array
	 */
	private $__config = array(
		'delay'		  => 3,
		'height'      => 50,
		'width'       => 150,
		'store'       => 'session', # session | cookie
		'cookiePath'  => '/',
		'cookieTime'  => 1000,
		'hashName'    => 'captcha',
		'font'        => array(            
			'Auth/font/roboto-light.ttf', 
			'Auth/font/roboto-thin.ttf'
			),
		'background'  => array(
			array(200, 200, 200)
			),
		'color'       => array(          
			array(0, 0, 0)
			),
		'lineColor'   => array(  
			array(255, 0, 0),
			array(0, 255, 0),
			array(0, 0, 255),
			array(0, 0, 0)
			),
		'minLines'    => 6,            
		'maxLines'    => 10,
		'minFSize'    => 20,
		'maxFSize'    => 30,
		'pxPerLetter' => 16,
		'minLength'   => 5,
		'maxLength'   => 7            
		);

	// Quick access to settings
	function __get($attr){
		if (array_key_exists($attr, $this->__config)) {
			return $this->__config[ $attr ];
		}
	}


	/**
	 * Changes configuration
	 * @param  array  $new
	 */
	public function config($new = array()){
		foreach ($new as $key => $value) {
			if (array_key_exists($key, $this->__config)) {
				$this->__config = array_replace($this->__config, $new);
			}
		}
	}


	/**
	 * Generates new captcha code
	 * @param  integer $length 
	 * @return string         
	 */
	private function generate($length){
		$chars = 'abdefhknrstyz23456789';
		$code = '';
		for ($i=0; $i < $length; $i++)
			$code .= substr($chars, rand(1, strlen($chars))-1, 1);

		return $code;
	}


	/**
	 * Store current captcha code
	 * @param string $code 
	 */
	private function set($code){
		$hash = md5(md5($code));
		
		setcookie($this->hashName, $hash, time() + $this->cookieTime, $this->cookiePath);
	}


	/**
	 * Compare codes
	 * @param  string $code 
	 * @return boolean       
	 */
	public function check($code){
		$hash = md5(md5($code));

		return isset($_COOKIE[ $this->hashName ])?($hash == $_COOKIE[ $this->hashName ]):false;
	}


	/**
	 * Clear current data
	 */
	public function clear(){
		setcookie($this->hashName, '', time() - 3600, $this->cookiePath);
	}


	/**
	 * Make captcha
	 */
	public function captcha(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                   
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", 10000) . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");         
        header("Cache-Control: post-check=0, pre-check=0", false);           
        header("Pragma: no-cache");                                           
        header("Content-Type:image/png");

        // Get random data
        $linesNum = rand($this->minLines, $this->maxLines);
        $font = $this->font[ rand(1, count($this->font))-1 ];
        $bg = $this->background[ rand(1, count($this->background))-1 ];
        $color = $this->color[ rand(1, count($this->color))-1 ];
        $text = $this->generate( rand($this->minLength, $this->maxLength) );
        $this->set($text);

        // Create image
        $img = imagecreatetruecolor($this->width, $this->height);

        // Fill image bg
        $imgBG = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
        imagefill($img, 0, 0, $imgBG);

        // Draw text
        $x = rand(0, floor(0.1 * $this->width));
        $yStart = floor(0.7 * $this->height);
        for ($i=0; $i < strlen($text); $i++) { 
        	$letter = substr($text, $i, 1);
        	$x += $this->pxPerLetter;
        	$y = rand(-5, 5) + $yStart;
        	$colorImg = imagecolorallocate($img, $color[0], $color[1], $color[2]);
        	imagettftext($img, 
        				 rand($this->minFSize, $this->maxFSize),
        				 rand(2, 4),
        				 $x, $y, $colorImg, $font, $letter);
        }

        // Draw lines
        for ($i = 0; $i < $linesNum; $i++){
        	$lineColor = $this->lineColor[ rand(1, count($this->lineColor))-1 ];
            $lineImgColor = imagecolorallocate($img, $lineColor[0], $lineColor[1], $lineColor[2]);
            imageline($img, 
            		  rand(0, $this->width), 
            		  rand(0, $this->height), 
            		  rand(0, $this->width), 
            		  rand(0, $this->height), 
            		  $lineImgColor);
        }

        // Output image
        ImagePNG ($img);	
        ImageDestroy ($img);	
	}
}


?>