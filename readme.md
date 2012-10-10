# A simple PHP CAPTCHA Component

_Based on_
_Cory LaViska's work for A Beautiful Site, LLC. (http://abeautifulsite.net/)_
_Dual licensed under the MIT / GPLv2 licenses_


## Demo

http://labs.abeautifulsite.dev/simple-php-captcha/


## Requirements

* Requires PHP GD library
* Background images must be in PNG format
* Fonts can be either TTF or OTF format
* Tested CakePHP 2.X


## Usage

### Files

Place the CaptchaComponent.php file in app/Controller/Component/
Place the background.png and ttf file in app/webroot/img
Create a app/webroot/img/captcha/ directory, writable by apache

### Declare the component in your Controller

`class UsersController extends AppController {

	public $components = array('Captcha');
`

### Declare your captcha in yor Controller
`
$this->Captcha->create(
	array(
		'images_url'=>'/img/captcha/',
		'images_path'=>WWW_ROOT.DS.'img/captcha/',
		'assets_path'=>WWW_ROOT.DS.'img/'
	)
);
$this->Session->write('Catcha.code',$this->Captcha->code());
$this->set('captcha_url',$this->Captcha->store());
`
### Check if the captcha has been properly entered in your Controller
`
public function token($id = null,$token = null) {

	$this->User->recursive = 0;
	$this->User->id = $id;
	if (!$this->User->exists()) {
		throw new NotFoundException(__('Invalid user'));
	}
	$user = $this->User->read(null, $id);
	
	if ($user['User']['token'] != $token) {
		throw new NotFoundException(__('Invalid token'));
	}

	if ($this->request->is('post') || $this->request->is('put')) {
		// check captcha
		if($this->request->data['User']['code'] == $this->Session->read('Catcha.code')){
`

### display the image in the View
inside your form form
	
`<p><img src="<?php echo $captcha_url; ?>" alt="captcha"></p>
<dl>
	<dt><?php echo __('Code'); ?></dt>
	<dd>
		<?php echo $this->Form->input('code',array('label'=>false,'type'=>'text','value'=>'')); ?>
	</dd>
</dl>`