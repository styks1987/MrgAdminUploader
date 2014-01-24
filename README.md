This adds a frontend to upload and resize images in the Cakephp 2.x framework

## Dependencies

* jQuery
* jQuert UI
* "mjohnson/uploader": "4.*"

## In your model

If you were adding this functionality to the slide model

```php5
	var $hasOne = array(
		'Image'=>[
			'className'=>'MrgAdminUploader.Attachment',
			'foreignKey'=>'foreign_key',
			'conditions'=>[
				'Image.model'=>'Slide'
			]
		]
	);
```

## In your controller

```php5
	public function admin_edit($id = null) {
		if (!$this->Slide->exists($id)) {
			throw new NotFoundException(__('Invalid slide'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			if(empty($this->request->data['Image']['img']['name']) && !empty($this->request->data['Image']['image_storage'])){
				// No new file was uploaded so lets persist the old one.
				// If they made transformations, we will catch that too.
				$this->request->data['Image']['img'] = 'http://'.$_SERVER['SERVER_NAME'].$this->request->data['Image']['image_storage'];
			}

			if ($this->Slide->saveAll($this->request->data)) {
				$this->Session->setFlash(__('The slide has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The slide could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Slide.' . $this->Slide->primaryKey => $id));
			$this->request->data = $this->Slide->find('first', $options);
		}
		$sites = $this->Slide->Site->find('list', array('fields'=>array('id', 'name')));
		$this->set(compact('sites'));
	}
```

## Include it in your view

```php5
	echo
	$this->Html->div('row',
		$this->Html->div('col-md-6 image_preview',
			$this->Html->image((!empty($this->data['Image']['resized']) && file_exists(WWW_ROOT.$this->data['Image']['resized'])) ? $this->data['Image']['resized']:'no_image.gif').
			$this->Html->link('Edit Image', 'javascript:void(0)', array('onclick'=>'$("#image_upload.images").image_uploader("enable_image_editing", this)'))
		)
	).
	$this->Html->div('row', $this->Html->div('col-md-12', $this->Form->end(array('label'=>'Save', 'class'=>'btn btn-success')))).
	$this->ImageEditor->init('Slide.Image', 'Image must be at least 1170x510 pixels').
	$this->Form->end().
	$this->Js->writeBuffer();
```