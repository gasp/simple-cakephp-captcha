<?php 

/** 
 * SimpleCaptchaComponent
 * 
 * @version 1.0 
 * @author gaspard from freelancis.com
 * @author Cory LaViska for A Beautiful Site, LLC.
 * 	@url http://abeautifulsite.net/blog/2011/01/a-simple-php-captcha-script/
 * 	@source https://github.com/claviska/simple-php-captcha
 * @license MIT Style License 
 */ 
class CaptchaComponent extends Component { 
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
		'shadow_offset_y' => 2,
		'images_path' => '.',
		'images_url' => '.'
	);
	
	/**
	 * creation method
	 *
	 * @param array $config 
	 * @return string $code
	 * @author gaspard
	 */
	public function create($config=array()) {

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
		if( $this->config['images_path'] == '.')	$this->config['images_path'] = './';
		if( $this->config['images_url'] == '.')		$this->config['images_url'] = './';
		
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
	 * code displays the current code (small helper)
	 *
	 * @return string code
	 * @author gaspard
	 */
	public function code() {
		return $this->config['code'];
	}

	/**
	 * stores the image
	 *
	 * @param string $value 
	 * @return string $imageurl
	 * @author gaspard
	 */
	public function store() {
		$hash = md5($this->config['code']);
		$captcha = $this->generate();

		// image path
		$image_fullpath = $this->config['images_path'].$hash.'.png';
		$image_fullurl = $this->config['images_url'].$hash.'.png';
		
		// Create
		if(!imagepng($captcha,$image_fullpath)){
			throw new Exception('could not write file '.$image_fullpath);
		}
		
		return $image_fullurl;
		
	}
	
	/**
	 * generate image
	 *
	 * @return Object image (to store or to display)
	 * @author gaspard
	 */
	public function generate(){
		
		// Pick random background, get info, and start captcha
		$background = $this->config['png_backgrounds'][rand(0, count($this->config['png_backgrounds']) -1)];
		$background = $this->config['assets_path'].DIRECTORY_SEPARATOR.$background;
		list($bg_width, $bg_height, $bg_type, $bg_attr) = getimagesize($background);
		
		
		$captcha = imagecreatefrompng($background);
	    imagealphablending($captcha, true);
	    imagesavealpha($captcha , true);

		$color = $this->hextorgb($this->config['color']);
		$color = imagecolorallocate($captcha, $color['r'], $color['g'], $color['b']);
		
		// Determine text angle
		$angle = rand( $this->config['angle_min'], $this->config['angle_max'] ) * (rand(0, 1) == 1 ? -1 : 1);

		// Select font randomly
		$font = $this->config['assets_path'].$this->config['fonts'][rand(0, count($this->config['fonts']) - 1)];

		// Verify font file exists
		if( !file_exists($font) ) throw new Exception('Font file not found: ' . $font);

		//Set the font size.
		$font_size = rand($this->config['min_font_size'], $this->config['max_font_size']);
		$text_box_size = imagettfbbox($font_size, $angle, $font, $this->config['code']);

		// Determine text position
		$box_width = abs($text_box_size[6] - $text_box_size[2]);
		$box_height = abs($text_box_size[5] - $text_box_size[1]);
		$text_pos_x_min = 0;
		$text_pos_x_max = ($bg_width) - ($box_width);
		$text_pos_x = rand($text_pos_x_min, $text_pos_x_max);			
		$text_pos_y_min = $box_height;
		$text_pos_y_max = ($bg_height) - ($box_height / 2);
		$text_pos_y = rand($text_pos_y_min, $text_pos_y_max);

		// Draw shadow
		if( $this->config['shadow'] ){
			$shadow_color = $this->hextorgb($this->config['shadow_color']);
		 	$shadow_color = imagecolorallocate($captcha, $shadow_color['r'], $shadow_color['g'], $shadow_color['b']);
			imagettftext($captcha, $font_size, $angle, $text_pos_x + $this->config['shadow_offset_x'], $text_pos_y + $this->config['shadow_offset_y'], $shadow_color, $font, $this->config['code']);	
		}

		// Draw text
		imagettftext($captcha, $font_size, $angle, $text_pos_x, $text_pos_y, $color, $font, $this->config['code']);

		return $captcha;
	}
	
	/**
	 * hextorgb method recodes hex2rgb function
	 * if it is native, let's just use it
	 * @param string HEX code
	 * @return array or string RGB code
	 */
	private function hextorgb($hex_str, $return_string = false, $separator = ','){
		if( !function_exists('hex2rgb') ) {
			$hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
			$rgb_array = array();
			if( strlen($hex_str) == 6 ) {
				$color_val = hexdec($hex_str);
				$rgb_array['r'] = 0xFF & ($color_val >> 0x10);
				$rgb_array['g'] = 0xFF & ($color_val >> 0x8);
				$rgb_array['b'] = 0xFF & $color_val;
			} elseif( strlen($hex_str) == 3 ) {
				$rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
				$rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
				$rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
			} else {
				return false;
			}
			return $return_string ? implode($separator, $rgb_array) : $rgb_array;
		}
		else return hex2rgb($hex_str, $return_string = false, $separator = ',');
	}

}