<?php


	/* EditableImageBehavior
	 * Description: This behavior persists the crop coordinates for an image.
	 * This will encode and decode the coordinates
	*/
	class CropCoordinatesBehavior extends ModelBehavior{


		public function setup(Model $Model, $settings = array()) {
			
		}
		// Set the correct location for the resized and thumb sizes. 
		function editableImageBeforeUpload($Model, $options){
			$this->model = $Model;
			// Not sure why this works but check the image editor component for how this gets replaced.
			// If not, included the image will continuously append a -1
			$options['append'] = 'appended';
			if(!empty($this->model->data[$this->model->alias]['crop_x1'])){

				$options['transforms']['thumb']['location'] = 'center';

				// Has to be crop otherwise it will not modify the image
				$options['transforms']['resized']['aspect'] = false;
				$options['transforms']['resized']['method'] = 'crop';

				$options = $this->set_height_width($options);

				$options['transforms']['resized']['location'] = [
																  $this->model->data[$this->model->alias]['crop_x1'],
																  $this->model->data[$this->model->alias]['crop_y1'],
																  $this->model->data[$this->model->alias]['crop_width'],
																  $this->model->data[$this->model->alias]['crop_height']
																];

			}
			return $options;
		}

		/**
		 * So if height is 0, set the correct height based on the aspect of the crop
		 *
		 * Date Added: Thu, Aug 21, 2014
		 */


		function set_height_width($options){
			$set_height = $options['transforms']['resized']['height'];
			$set_width = $options['transforms']['resized']['width'];

			$cropped_height = $this->model->data[$this->model->alias]['crop_height'];
			$cropped_width = $this->model->data[$this->model->alias]['crop_width'];

			if($set_height == 0){
				$height = $cropped_height * $set_width / $cropped_width;
				$options['transforms']['resized']['height'] = $height;
			}/*elseif(1==2){
				$crop_width = $this->model->data[$this->model->alias]['crop_width'] - $this->model->data[$this->model->alias]['crop_x1'];
				$crop_height = $this->model->data[$this->model->alias]['crop_height'] - $this->model->data[$this->model->alias]['crop_y1'];

				$set_height = $set_width * $crop_height / $crop_width;
				if($set_height < 0){
					$set_height = $set_height * -1;
				}
				$options['transforms']['resized']['height'] = $set_height;
			}*/
			return $options;
		}
	}
?>
