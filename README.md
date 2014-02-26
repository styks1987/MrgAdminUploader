This adds a frontend to upload and resize images in the Cakephp 2.x framework

## Dependencies

* jQuery
* jQuert UI
* "mjohnson/uploader": "4.*"

## Included Javascript Files

* ImageAreaSelect v1.0 (http://odyniec.net/projects/imgareaselect/)
* jQuery.upload v1.0.2 (http://lagoscript.org)

## CakeHelpers

* JsHelper

## Attachment Schema

You will need to have a table called attachments. A schema file is provided.
cake schema create --plugin MrgAdminUploader

## In your layout

```php5
	// You must include these files
	echo $this->Html->script('jquery.1.10.js');
	echo $this->Html->script('jquery-ui-1.10.4.core.widget.js');

	// You must also have this at the bottom of your view
	echo $this->Js->writeBuffer();
```

## In your model

If you were adding this functionality to the slide model

```php5
	var $hasOne = array(
		'Image'=>[
			'className'=>'MrgAdminUploader.Attachment',
			'foreignKey'=>'foreign_key',
			'conditions'=>[
				'Image.model'=>'Slide'
			],
			// This allows you to control the transformations and settings
			// For the uploader behavior
			'Behaviors'=>[
				'Attachment'=>[
					'img'=>[
						'transforms'=>[
							'resized'=>[
								'width'=>1170,
								'height'=>510
							],
							'thumb'=>[
								'width'=>192,
								'height'=>192
							]
						]
					]
				]
			]
		]
	);
```

## In your controller

```php5
	public $components = ['MrgAdminUploader.ImageEditor'];
	public $helpers = ['MrgAdminUploader.ImageEditor'];
	
	public function admin_edit($id = null) {
		if (!$this->Slide->exists($id)) {
			throw new NotFoundException(__('Invalid slide'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			// If the user has uploaded an iamge before
			// make sure it does not get deleted if no image
			// was uploaded

			// Also, if transformations were made
			// This persists that
			$this->ImageEditor->persist_image();
			// Set the attachment settings
			// This may need to be moved.
			$this->ImageEditor->behavior_settings();

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
		// This loades our options for display
		$imageEditorOptions = $this->ImageEditor->get_attachment_options();
		$this->set(compact('imageEditorOptions'));
	}
```

## Include it in your view

```php5
	echo
	$this->Html->div('row',
		$this->Html->div('col-md-6 image_preview',
			$this->ImageEditor->editor($this->data)
		)
	).
	$this->Html->div('row', $this->Html->div('col-md-12', $this->Form->end(array('label'=>'Save', 'class'=>'btn btn-success')))).
$this->ImageEditor->init('Slide.Image', 'Image must be at least '.$imageEditorOptions['width'].'x'.$imageEditorOptions['height'].' pixels', 'image_upload', $imageEditorOptions).
	$this->Form->end().
	$this->Js->writeBuffer();
```
