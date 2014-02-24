<div id="dropbox">
    <span class="message">Drop images here to upload. <br /><i>(they will only be visible to you)</i></span>
</div>

	<?php
		// Inline Image Uploading
		$this->Html->script('jquery.plugins/jquery.filedrop', array("inline"=>false));
		$this->Html->script('Image.uploader', array("inline"=>false));
		$this->Html->css('Image.uploader', 'stylesheet', array("inline"=>false));
	?>

