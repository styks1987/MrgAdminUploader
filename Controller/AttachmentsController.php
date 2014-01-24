<?php
	class AttachmentsController extends AppController{
		var $components = array('RequestHandler');


		public function index() {
			$conds = array();
			if(!empty($this->request->query['foreign_key'])){
				$conds['Attachment.foreign_key'] = $this->request->query['foreign_key'];
			}
			$attachments = Hash::extract($this->Attachment->find('all', array('conditions'=>$conds)), '{n}.Attachment');
			$this->set(array(
				'attachments' => $attachments,
				'_serialize' => array('attachments')
			));
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

	}

?>
