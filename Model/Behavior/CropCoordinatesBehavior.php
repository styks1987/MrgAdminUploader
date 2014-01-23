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
			$options['append'] = time();
			if(!empty($Model->data[$Model->alias]['crop_x1'])){
				// To make sure the aspect ratio is correct, we need to determine which side is the biggest
				// And use the smallest size for the thumbnail size
				// This wouldn't be a problem if the image was the same aspect ratio.
				if($Model->data[$Model->alias]['crop_width'] >= $Model->data[$Model->alias]['crop_height']){
					$size = $Model->data[$Model->alias]['crop_height'];
				}else{
					$size = $Model->data[$Model->alias]['crop_width'];
				}
				// Needs to be updated to find best possible image size
				// Use the coordinates to get the proper scaling
				// crop is based off of resized aspect. Need to recalibrate aspect for thumb based on thumb height and width.

				//debug($Model->data[$Model->alias]);

				//$width_multiplier = number_format($Model->data[$Model->alias]['crop_width']/$options['transforms']['resized']['width'], 0);
				//$height_multiplier = number_format($Model->data[$Model->alias]['crop_height']/$options['transforms']['resized']['height'], 0);

				//$thumb_crop_coords[] = $options['transforms']['thumb']['width'] * $width_multiplier;
				//$thumb_crop_coords[] = $options['transforms']['thumb']['height'] * $height_multiplier;
				$options['transforms']['thumb']['location'] = 'center';/*array_merge(array_slice(array_values($Model->data[$Model->alias]), 0 ,2), $thumb_crop_coords);*/

				//debug($options['transforms']['thumb']['location'] );
				//exit;
				$options['transforms']['resized']['location'] = [
																  $Model->data[$Model->alias]['crop_x1'],
																  $Model->data[$Model->alias]['crop_y1'],
																  $Model->data[$Model->alias]['crop_width'],
																  $Model->data[$Model->alias]['crop_height']
																];
			}
			return $options;
		}
	}
?>
