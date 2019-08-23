<?php
	class AttachmentsController extends AppController{
		var $components = array('RequestHandler');


		public function admin_index() {
			$conds = $order = array();
			if(!empty($this->request->query['foreign_key'])){
				$conds['Attachment.foreign_key'] = $this->request->query['foreign_key'];
				$conds['Attachment.model'] = $this->request->query['model'];
			}
			if($this->Attachment->hasField('order_by')){
				$order = 'Attachment.order_by ASC';
			}
			$attachments = Hash::extract($this->Attachment->find('all', array('conditions'=>$conds, 'order'=>$order)), '{n}.Attachment');
			$this->set(array(
				'attachments' => $attachments,
				'_serialize' => array('attachments')
			));
		}

		public function admin_delete($id) {
			if ($this->Attachment->delete($id)) {
				$message = 'Deleted';
			} else {
				$message = 'Error';
			}
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			exit;
		}

		public function admin_update($id) {
			if($this->request->is('post') || $this->request->is('put') ){
				$model = $this->request->data['model'];
				$behavior = 'Attachment';
				$class = 'Image';
				App::import('Model', $model);
				$this->$model = new $model();
				$defaultSettings = $this->Attachment->Behaviors->{$behavior}->settings['Attachment'];
				$defaultSettings['img']['cleanup'] = false;
				$defaultSettings['file']['cleanup'] = false;
				$this->Attachment->Behaviors->{$behavior}->settings['Attachment'] = hash::merge(
					$defaultSettings,
					$this->$model->hasMany[$class]['Behaviors'][$behavior]
				);
				if ($this->Attachment->save($this->request->data)) {
					$message = 'Updated';
				} else {
					$message = 'Error';
				}
			}
			$attachment = $this->Attachment->find('first', array('conditions' => ['id' => $id]));
			$this->_exit_status($attachment['Attachment']);
		}

		/**
		 * return all images for a particular id
		 *
		 * Date Added: Fri, Jan 24, 2014
		 */

		//public function all($foreign_key = 1, $model) {
		//	$this->ProjectImage->recursive = 0;
		//	$this->paginate = array('conditions'=>array('foreign_key'=>$foreign_key));
		//	$attachments = $this->paginate();
		//	if($this->RequestHandler->isAjax()){
		//		$attachments = Hash::extract($attachments, '{n}.'.$model);
		//		echo json_encode($attachments);
		//		exit;
		//	}else{
		//		$this->set(compact('attachments'));
		//	}
		//}


		/**
		 * upload a tmp file to the server so that we can let the user modify it
		 *
		 * Date Added: Thu, Jan 23, 2014
		 */
		/**
		*  Function Name: admin_upload
		*  Description: Upload a tmp file so that we can let the user modify it
		*  Date Added: Tue, Feb 19, 2013
		*/
		public function admin_ajax_upload($model = 'Image'){
			if(!is_dir(APP.'webroot'.$this->request->data['tmp_upload_dir'])){
				mkdir(APP.'webroot'.$this->request->data['tmp_upload_dir']);
			}
			$tmp_filename = $_FILES['data']['tmp_name'][$model]['img'];
			$filename = $_FILES['data']['name'][$model]['img'];
			$error = $_FILES['data']['error'][$model]['img'];

			$name = time().$filename;
			if(!$error){
				if(move_uploaded_file($tmp_filename, APP.'webroot'.$this->request->data['tmp_upload_dir'].'/'.$name)){
					$response = array('url'=>$name, 'error'=>false);
					echo json_encode($response);
				}else{
					echo json_encode(array('url'=>$name, 'error'=>'The uploaded file could not be saved. Please try again. If the problem persists, please contact us.'));
				}
			}else{
				if($error == 1){
					$max_size = ini_get('upload_max_filesize');
					echo json_encode(array('url'=>$name, 'error'=>'Your image is too large. We only allow images '.$max_size.' or smaller'));
				}else{
					$this->log('File upload error '.$error);
					echo json_encode(array('url'=>$name, 'error'=>'We experienced error code '.$error.' while attempting to upload your file. If this problem persists, please contact us.'));
				}
			}
			exit;
		}


		/**
		 * upload multiple files
		 *
		 * Date Added: Fri, Jan 24, 2014
		 */

		public function admin_multifile_ajax_upload($foreign_key, $model='Other'){
			// Get the image file settings for the upload

			// TODO make this more abstract
			if($model != 'Other'){
				$behavior = 'Attachment';
				$class = 'Image';
				App::import('Model', $model);
				$this->$model = new $model();
				$this->Attachment->Behaviors->{$behavior}->settings['Attachment'] = hash::merge(
					$this->Attachment->Behaviors->{$behavior}->settings['Attachment'],
					$this->$model->hasMany[$class]['Behaviors'][$behavior]
				);
			}


			$this->request->data['Attachment']['foreign_key'] = $foreign_key;
			$this->request->data['Attachment']['model'] = $model;
			if($this->Attachment->save($this->request->data)){
				$image_name = $this->Attachment->field('thumb');
				$return_data['status'] = 1;
				$return_data['message'] = 'Your image was successfully updated';
				$return_data['file_url'] = $image_name;
			}else{
				$errors = $this->Attachment->invalidFields();
				$message = $errors['img'][0];
				$return_data['status'] = 0;
				$return_data['message'] = $message;
			}
			$this->_exit_status($return_data);
		}

		/**
		 * sort multiple files for a single foreign_key
		 * requires the order field on the attachments table
		 *
		 * Date Added: Tue, Sep 09, 2014
		 */
		public function admin_update_order(){
			$saved = $this->Attachment->saveAll($this->request->data);
			if($saved){
				$response = ['status'=>1, 'message'=>'Order Updated'];
			}else{
				$response = ['status'=>0, 'message'=>'Order failed to update'];
			}
			$this->_exit_status($response);
		}



		/**
		 * get the file extension
		 *
		 * Date Added: Fri, Jan 24, 2014
		 */

		private function _get_extension($file_name){
			$ext = explode('.', $file_name);
			$ext = array_pop($ext);
			return strtolower($ext);
		}
		/**
		 * echo out json
		 *
		 * Date Added: Fri, Jan 24, 2014
		 */

		private function _exit_status($return_data){
			echo json_encode($return_data);
			exit;
		}

		// NEEDS TO BE ITS OK PLUGIN
		// CKEDITOR FUNCTIONALITY

		/**
		*  Function Name: ckbrowse
		*  Description: Browse the images available on server through ckeditor
		*  Date Added: Tue, Jul 09, 2013
		*/
		public function ckbrowse(){
			$this->layout = 'Image.browse';
			$this->Attachment->recursive = 0;
			$this->set('attachments', $this->paginate());
		}

		/**
			*  Function Name: ckupload
			*  Description: Upload from within ckeditor
			*  Date Added: Tue, Jul 09, 2013
		*/
		public function ckupload(){
			$data['Attachment'] = [
				'model'=>'CKEDITOR',
				'foreign_key'=>0,
				'file'=>$_FILES['upload']
			];
			//debug($data); exit;
			if($this->Attachment->save($data)){
				$image_name = $this->Attachment->field('img');
				echo '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$this->params->query['CKEditorFuncNum'].', "'.$image_name.'");</script>';
				exit;
			}else{
				echo '<div style="height:100px; width:200px;">Something went wrong while uploading</div>';
				exit;
			}

		}

		// CKEDITOR FUNCTIONALITY END


		/**
		 * upload to the attachments database without tying it to anything
		 *
		 * Date Added: Fri, Mar 28, 2014
		 */

		public function file_upload(){
			$defaults = ['model'=>'Empty', 'foreign_key'=>0, 'name'=>'upload'];
			$options = array_merge($defaults, $this->request->data);

			$filename = $_FILES[$options['name']]['name'];
			$extension = pathinfo($filename, PATHINFO_EXTENSION);

			if(!strstr($this->request->data['types'], $extension)){
				$this->_exit_status(['status'=>0, 'error'=>'You have tried to upload an invalid file type. Accepted file types are '.$this->request->data['types']]);
				exit;
			}

			$data['Attachment'] = [
				'model'=>$options['model'],
				'foreign_key'=>$options['foreign_key'],
				'file'=>$_FILES[$options['name']]
			];

			if($this->Attachment->save($data)){
				$url = $this->Attachment->field('img');
				$this->_exit_status(['field'=>$options['name'], 'model'=>$options['model'], 'url'=>$url, 'status'=>1, 'attachment_id'=>$this->Attachment->id]);
				exit;
			}else{
				$this->_exit_status(['status'=>0, 'error'=>'We could not upload your file. Please check the filesize and try again.']);
				exit;
			}
		}


	}

?>
