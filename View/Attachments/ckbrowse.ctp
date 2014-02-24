<?php
	//foreach($images as $i){
	//	static $image_html = '';
	//	$image_html .= $this->Html->div('pod',
	//		$this->Html->link($this->Html->image($i['Image']['original']), 'javascript:void(0)', array("onclick"=>"window.opener.CKEDITOR.tools.callFunction( ".$this->params->query['CKEditorFuncNum'].", '".$i['Image']['original']."' ); window.close();", 'escape'=>false)).
	//		$this->Html->tag('p', $i['Image']['id']).
	//		$this->Js->link('delete', '/images/delete/'.$i['Image']['id'], array('method'=>'post','success'=>'$("#delete_'.$i['Image']['id'].'").remove()','confirm'=>'Are you sure you want to delete this image? This will remove it from all posts and pages.'))
	//		,array('id'=>'delete_'.$i['Image']['id'])
	//	);
	//}

	echo "Server browsing is not available. Please contact us to create it.";exit;
	//echo $image_html;

	echo $this->Html->div('attachments', '');

	echo $this->element('Image.uploader');




	echo $this->Js->writeBuffer();
?>
<script type="text/javascript">

	jQuery(document).ready(function () {
        init_content_editor();
		jQuery('body').midgardCreate({
			url: function () {return '/attachments/'}
		})
		jQuery('body').midgardCreate('setEditorForProperty', 'title', 'simple_editor');
	});
	<?php if(!empty($this->params->query['CKEditorFuncNum'])): ?>
		var CKEditorFuncNum = <?php echo $this->params->query['CKEditorFuncNum']; ?>;
	<?php endif; ?>
	ImageModel = Backbone.Model.extend({

	})

	image = new ImageModel({
		url: '/attachments.json'
	})



	ImageView = Backbone.View.extend({
		className: 'pod',
		initialize: function(){
			this.model.on('delete', this.deleteImage, this);
		},
		template: _.template(
			'<div class="image_holder"><img src="<%= original %>" alt="<%= title %>" /></div>'+
			'<h4 property="title" class="title"><%= title %></h4>'+
			'<div class="btn btn-danger delete">Delete</div>'
		),
		events:{
			"click img":"_ckselect",
			"click .delete":"deleteImage"
		},

		render: function () {
			var attributes = this.model.toJSON();
			this.$el.html(this.template(attributes));
			this.$el.attr({'typeOf':'Image', 'about':'/attachments/'+this.model.get('id')});
			return this;
		},

		deleteImage: function () {
			this.model.destroy();
			this.remove();
		},

		// return the selected image back to ckeditor
		_ckselect: function () {
			//if (typeof CKeditorFuncNum != 'undefined') {
				window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum, this.model.get('original'));
				window.close();
			//}else{
				// Not working needs help with choosing a specific image
				//console.log(';here');
				//window.selected_image = this.model.get('original');
				//$('.image_editor_overlay').trigger('choose_image');
			//}
		}
	});

	ImageList = Backbone.Collection.extend({
		url: '/attachments.json',
		model:ImageModel,
		parse: function (response){
			return response.attachments
		}
	})

	imageList = new ImageList();

	imageList.fetch();

	ImageListView = Backbone.View.extend({
		className:'image_collection',
		initialize: function (){
			this.collection.on('add', this.addOne, this);
			this.collection.on('reset', this.addAll, this);

		},
		addOne: function(image){
			var imageView = new ImageView({model:image});
			this.$el.attr({'rel':'hasPart', 'about':'/attachments/'});
			this.$el.append(imageView.render().el);
		},
		addAll: function (){
			this.collection.forEach(this.addOne, this);
		},

		render: function (){
			this.addAll();
		}
	})

	imageListView = new ImageListView({collection:imageList});

	imageListView.render();

	$('.attachments').html(imageListView.el);

</script>
