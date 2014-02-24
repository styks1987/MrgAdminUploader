<!-- This layout is used when displaying images in the ckeditor -->
<html>
	<?php
		echo $this->Html->css('../js/bootstrap/css/bootstrap.min');
		echo $this->Html->css('../js/create/examples/font-awesome/css/font-awesome');
		echo $this->Html->css('../js/create/themes/midgard-notifications/midgardnotif');
		echo $this->Html->css('../js/create/themes/insertimage');
		echo $this->Html->css('../js/create/themes/create-ui/css/create-ui');
		echo $this->Html->css('main');
		echo $this->Html->css('Image.browse');


		//echo $this->Html->script('create/deps/jquery-1.9.1.min');
		echo $this->Html->script('http://code.jquery.com/jquery-1.9.1.js');
		echo $this->Html->script('create/deps/jquery-ui-1.10.2.min');
		echo $this->Html->script('create/deps/mousetrap.min');
		echo $this->Html->script('create/deps/underscore-min');
		echo $this->Html->script('create/deps/backbone-min');
		echo $this->Html->script('create/deps/vie-min');

		// Tag Widget
		echo $this->Html->script('create/deps/jquery.tagsinput.min');
		// rdfquery and annotate are only needed for the Hallo annotations plugin
		echo $this->Html->script('create/deps/jquery.rdfquery.min');
		echo $this->Html->script('create/deps/annotate-min');

		echo $this->Html->script('create/deps/rangy-core-1.2.3');

		echo $this->Html->script('http://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.0.1/ckeditor.js');

		// createjs.org - Inline editing
		echo $this->Html->script('create/examples/create');


		echo $this->Html->script('bootstrap/js/bootstrap.min');

		// This is our default script file
		echo $this->Html->script('main');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');

	?>
	<body class="browse_images">

		<?php echo $content_for_layout; ?>

	</body>
</html>
