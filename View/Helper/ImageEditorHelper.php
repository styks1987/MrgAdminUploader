<?php
	App::uses('Helper', 'View');


	class ImageEditorHelper extends AppHelper{

		var $helpers = array('Html', 'Form', 'Js');

		function init($model, $instructions='', $id='image_upload'){
			// Load our required assets
			$this->Html->css('MrgAdminUploader.imageareaselect/imgareaselect-animated', 'stylesheet', ['inline'=>false]);
			$this->Html->script('MrgAdminUploader.jquery.upload.1.0.2', ['inline'=>false]);
			$this->Html->script('MrgAdminUploader.image_editor_widget', ['inline'=>false]);
			$this->Html->script('MrgAdminUploader.imageareaselect/jquery.imgareaselect', ['inline'=>false]);


			list($parent_model, $image_model) = explode('.',$model);
			$plural_class_name = Inflector::underscore(Inflector::pluralize($image_model));

			// We need to instantiate the javascript
			// $this->Js->writeBuffer(); required in the view

			$this->Js->buffer("jQuery('#".$id.".".$plural_class_name."').image_uploader();");


			return
			$this->Html->div('',
				$this->Html->tag('span',
				$this->Form->input($image_model.'.img', array('type'=>'file', 'id'=>$id, 'class'=>$plural_class_name)).
				$this->Form->input($image_model.'.model', array('type'=>'hidden', 'value'=>$parent_model)).
				$this->Form->input($image_model.'.id', array('type'=>'hidden', 'value'=>(!empty($this->data[$image_model]['id']))?$this->data[$image_model]['id']:'')).
				$this->Html->tag('p', $instructions, array('style'=>'margin:0;')).
				$this->Form->hidden($image_model.'.image_storage', array('id'=>'image_storage', 'value'=>(isset($this->data[$image_model])) ? $this->data[$image_model]['img']:'')).
				$this->Form->hidden($image_model.'.crop_x1', array('id'=>'crop_x')).
				$this->Form->hidden($image_model.'.crop_y1', array('id'=>'crop_y')).
				$this->Form->hidden($image_model.'.crop_width', array('id'=>'crop_width')).
				$this->Form->hidden($image_model.'.crop_height', array('id'=>'crop_height')).
				$this->Html->image((!empty($this->data[$image_model]['img'])) ? $this->data[$image_model]['img']:'no_image.gif', array('id'=>'image_select', 'width'=>'300px', 'alt'=>'Choose an image and it will appear here'))
				),
				array('id'=>'editing_tools', 'style'=>'display:none;')
			);
		}

		/**
		 * Multi File Uploader
		 *
		 * Date Added: Fri, Jan 24, 2014
		 */

		function multi_file_init($foreign_key_id = 0, $model){
			$this->Html->css('MrgAdminUploader.multifile_uploader', 'stylesheet', array("inline"=>false));
			$this->Html->script('MrgAdminUploader.jquery.filedrop', array("inline"=>false));
			$this->Html->script('MrgAdminUploader.jquery.nested_sortable', array("inline"=>false));
			$this->Html->script('MrgAdminUploader.multifile_uploader', array("inline"=>false));
			return
				$this->Html->div('',
					$this->Html->div('message', 'Drop images here to upload'),
					['id'=>'dropbox']
				).
				$this->Html->scriptBlock('window.attachment_foreign_key = '.$foreign_key_id.'; window.attachment_foreign_model = "'.$model.'";').
				$this->Html->div('attachments', '');

		}

	}

?>
