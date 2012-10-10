<?php  
/** 
 * SimpleCaptchaHelper
 * 
 * @version 1.0 
 * @author gaspard from freelancis.com
 * @author Cory LaViska for A Beautiful Site, LLC.
 * 	@url http://abeautifulsite.net/blog/2011/01/a-simple-php-captcha-script/
 * 	@source https://github.com/claviska/simple-php-captcha
 * @license MIT Style License 
 */ 
//App::uses('Helper', 'View');
class SimpleCaptchaHelper { 
//class SimpleCaptchaHelper extends AppHelper { 
	public $config = array(
		'code' => '',
		'min_length' => 5,
		'max_length' => 5,
		'assets_path' => '.',
		'png_backgrounds' => array('default.png'),
		'fonts' => array('times_new_yorker.ttf'),
		'characters' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
		'min_font_size' => 24,
		'max_font_size' => 30,
		'color' => '#000',
		'angle_min' => 0,
		'angle_max' => 15,
		'shadow' => true,
		'shadow_color' => '#CCC',
		'shadow_offset_x' => -2,
		'shadow_offset_y' => 2
	);
	
	/**
	 * construction method
	 *
	 * @param array $config 
	 * @return string $code
	 * @author gaspard
	 */
	public function __construct($config=array()) {

		// Check for GD library
		if( !function_exists('gd_info') ) {
			throw new Exception('Required GD library is missing');
		}

		// Overwrite defaults with custom config values
		if( is_array($config) ) {
			foreach( $config as $key => $value ) $this->config[$key] = $value;
		}

		// Restrict certain values
		
		if( $this->config['assets_path'] == '.')	$this->config['assets_path'] = dirname(__FILE__);
		
		if( $this->config['min_length'] < 1 ) 	$this->config['min_length'] = 1;
		if( $this->config['angle_min'] < 0 ) 	$this->config['angle_min'] = 0;
		if( $this->config['angle_max'] > 10 ) 	$this->config['angle_max'] = 10;
		if( $this->config['angle_max'] < $this->config['angle_min'] ) $this->config['angle_max'] = $this->config['angle_min'];
		if( $this->config['min_font_size'] < 10 ) $this->config['min_font_size'] = 10;
		if( $this->config['max_font_size'] < $this->config['min_font_size'] ) $this->config['max_font_size'] = $this->config['min_font_size'];

		// Use milliseconds instead of seconds
		srand(microtime() * 100);

		// Generate CAPTCHA code if not set by user
		if( empty($this->config['code']) ) {
			$this->config['code'] = '';
			$length = rand($this->config['min_length'], $this->config['max_length']);
			while( strlen($this->config['code']) < $length ) {
				$this->config['code'] .= substr($this->config['characters'], rand() % (strlen($this->config['characters'])), 1);
			}
		}
	}

	/**
	 * stores the image
	 *
	 * @param string $value 
	 * @return string $imageurl
	 * @author gaspard
	 */
	public function store($value='') {
		# code...
	}

}