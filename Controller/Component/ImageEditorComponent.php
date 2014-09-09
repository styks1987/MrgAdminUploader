<?php

class ImageEditorComponent extends Component{


	public function initialize(Controller $controller) {
		parent::initialize($controller);
		$this->Controller = $controller;
	}

	/**
	 * persist an already uploaded image
	 * if they made transform changes, persist that too
	 *
	 * Date Added: Thu, Feb 13, 2014
	 */

	public function persist_image(){
		if(empty($this->Controller->request->data['Image']['img']['name']) && !empty($this->Controller->request->data['Image']['image_storage'])){
			// No new file was uploaded so lets persist the old one.
			// If they made transformations, we will catch that too.
			$this->Controller->request->data['Image']['img'] = 'http://'.$_SERVER['SERVER_NAME'].$this->Controller->request->data['Image']['image_storage'];
		}
	}

	/**
	 * Set settings for behavior
	 *
	 * Date Added: Thu, Feb 13, 2014
	 */

	public function behavior_settings($class = 'Image', $behavior = 'Attachment'){
		$this->Controller->{$this->Controller->modelClass}->Image->Behaviors->{$behavior}->settings[$class] = hash::merge(
			$this->Controller->{$this->Controller->modelClass}->Image->Behaviors->{$behavior}->settings[$class],
			$this->Controller->{$this->Controller->modelClass}->hasOne[$class]['Behaviors'][$behavior]
		);
	}

	/**
	 * Get options for the image editor so we can crop the
	 * image properly
	 *
	 * Date Added: Thu, Feb 13, 2014
	 */
	public function get_attachment_options(){
		$settings = $this->Controller->{$this->Controller->modelClass}->hasOne['Image']['Behaviors']['Attachment']['img']['transforms']['resized'];
		$height = (!empty($settings['height']))? $settings['height'] : false;
		$width = (!empty($settings['width']))? $settings['width'] : false;
		$aspect = ($height && $width)? $width.':'.$height : false;

		$image_editor_settings = [
				'max_width'=>$width, 'width'=>$width,
				'max_height'=>$height, 'height'=>$height,
				'aspect'=>$aspect
			];
		return $image_editor_settings;
	}
}
?>
