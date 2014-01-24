<?php
	class AttachmentsController extends AppController{
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

	}

?>
