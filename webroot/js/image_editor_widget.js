	// Image Uploader Widget
	// Admin section image cropping and image uploading frontend
	// Taken and refactored into a widget from the tigers site
	(function (jQuery, undefined) {
		$.widget("Merge.image_uploader",{
			options: {
				max_width:1170,
				max_height:510,
				aspect:'2:1',
				crop_x:'crop_x',
				crop_y:'crop_y',
				crop_width:'crop_width',
				crop_height:'crop_height',
				image_select:'image_select',
				tmp_upload_url:'/admin/mrg_admin_uploader/attachments/ajax_upload',
				// Must match what is in the Attachments controller
				tmp_upload_dir:'/files/uploads/'
			},

			_create:function (){
				var widget = this
				var img = new Image();
				img.src = $('#'+widget.options.image_select).attr('src');
				img.onload  = function(){
					widget._setup_imageselect(this);
				}
				widget.element.change(function() {
					$('.image_loader').toggle();
					var input = widget._parse_cake_input_name(widget.element.attr('name'));
					$(this).upload(widget.options.tmp_upload_url+'/'+input.model, {tmp_upload_dir:widget.options.tmp_upload_dir}, function(res) {
						res = $.parseJSON(res);
						if (!res.error) {
							$('#'+widget.options.image_select).attr('src', widget.options.tmp_upload_dir+'/'+res.url);
								var new_img = new Image();
								new_img.src = $('#'+widget.options.image_select).attr('src');
								new_img.onload  = function(){
									img_width = new_img.width;
									img_height = new_img.height;
									if(img_width >= widget.options.max_width && img_height >= widget.options.max_height){
										if($('#image_storage').is('*')){$('#image_storage').val(widget.options.tmp_upload_dir+'/'+res.url)}
										widget._clear_selection(this);
										widget._setup_imageselect(this);
									}else{
										$('input.file').val('');
										$('#'+widget.options.image_select).attr('src', '/img/no_image.gif');
										if($('#image_storage').is('*')){$('#image_storage').val('')}
										alert('Your image size is too small. Currently, your image is '+img_width+'px wide and '+img_height+'px tall. Please select a larger image.');
									}
								};
						}else{
							$('input.file').val('');
							$('#'+widget.options.image_select).attr('src', '/img/no_image.gif');
							if($('#image_storage').is('*')){$('#image_storage').val('')}
							alert(res.error);

						}
						if ($('.input_file').is('*')) {
							$('input.file').val($('input.file').val().replace("C:\\fakepath\\", ""))
						}
						$('.image_loader').toggle();
					});
				});
			},

			/*
			*  Function Name: enable_image_editing
			*  Short Description: Edit a featured image
			*  Date Added: Thu, Mar 07, 2013
			*/

			enable_image_editing: function (el){
				widget = this
				if($('#editing_tools').is('*')){
					$.when($(el).parent().html($('#editing_tools'))).then(widget.update_selection());
				}
			},
			/* Remove the selection values for a new image */
			/* Without this, changing an image sometimes causes wrong cropping */
			_clear_selection:function (){
				$('#'+widget.options.crop_x).val('');
				$('#'+widget.options.crop_y).val('');
				$('#'+widget.options.crop_width).val('');
				$('#'+widget.options.crop_height).val('');
			},

			_setup_imageselect: function (img){
				var widget = this
				var naturalWidth = img.width;
				var naturalHeight = img.height;
				var img_obj = $('#'+widget.options.image_select);
				var scaled_x = naturalWidth / img_obj.width();
				var scaled_y =naturalHeight / img_obj.height();
				var x1 = parseFloat($('#'+widget.options.crop_x).val()) / scaled_x;
				var y1 = parseFloat($('#'+widget.options.crop_y).val()) / scaled_y;
				var x2 = (parseFloat($('#'+widget.options.crop_x).val()) + parseFloat($('#'+widget.options.crop_width).val())) / scaled_x;
				var y2 = (parseFloat($('#'+widget.options.crop_y).val()) + parseFloat($('#'+widget.options.crop_height).val())) / scaled_y;

				if(isNaN(x1) || isNaN(x2) || isNaN(y1) || isNaN(y2)){
					// If the image hasn't been uploaded yet
					x1 = y1 = 0;
					x2 = widget.options.max_width / scaled_x;
					y2 = widget.options.max_height / scaled_y;
				}

				$('#'+widget.options.image_select).imgAreaSelect({x1:x1, x2:x2, y1:y1, y2:y2, minWidth: Math.round(widget.options.max_width / scaled_x), minHeight: Math.round( widget.options.max_height / scaled_y), aspectRatio:widget.options.aspect, handles: true, onSelectEnd:widget._set_dimensions});

			},

			/**
				*  Function Name: set_dimensions
				*  Short Description: Set the dimensions for the crop area
				*  Date Added: Tue, Feb 19, 2013
				*/

			_set_dimensions : function (img, selection){
				var img_obj = $(img).context;
				var scaled_x = img_obj.naturalWidth / img_obj.width;
				var scaled_y = img_obj.naturalHeight / img_obj.height;

				// sometimes we get problems when this is 0
				var x1 = Math.round(selection.x1 * scaled_x);
				var y1 = Math.round(selection.y1 * scaled_y);
				x1 = (x1 == 0)?1:x1;
				y1 = (y1 == 0)?1:y1;
				$('#'+widget.options.crop_x).attr('value', x1);
				$('#'+widget.options.crop_y).attr('value', y1);
				$('#'+widget.options.crop_width).attr('value', Math.round(selection.width * scaled_x));
				$('#'+widget.options.crop_height).attr('value', Math.round(selection.height * scaled_y));
			},

			update_selection : function(){
				widget = this
				var img = new Image();
				img.src = $('#'+widget.options.image_select).attr('src');
				img.onload  = function(){
					var naturalWidth = img.width;
					var naturalHeight = img.height;
					var img_obj = $('#'+widget.options.image_select);
					var scaled_x = naturalWidth / img_obj.width();
					var scaled_y =naturalHeight / img_obj.height();

					var ias = $('#'+widget.options.image_select).imgAreaSelect({aspectRatio:widget.options.aspect, instance: true, minWidth:widget.options.max_width/scaled_x, minHeight:widget.options.max_height/scaled_y });
					var x1 = parseFloat($('#'+widget.options.crop_x).val()) / scaled_x;
					var y1 = parseFloat($('#'+widget.options.crop_y).val()) / scaled_y;
					var x2 = (parseFloat($('#'+widget.options.crop_x).val()) + parseFloat($('#'+widget.options.crop_width).val())) / scaled_x;
					var y2 = (parseFloat($('#'+widget.options.crop_y).val()) + parseFloat($('#'+widget.options.crop_height).val())) / scaled_y;
					ias.setSelection(x1, y1, x2, y2, true);
					ias.setOptions({ show: true });
					ias.update();
				}
			},

			hide_selection: function(){
				var ias = $('#'+widget.options.image_select).imgAreaSelect({ instance: true });
				ias.setOptions({ hide: true });
				ias.update();
			},
			/**
			*  Function Name: parse_cake_input_name
			*  Short Description: Parse a cake specific input name
			*  data[Model][field]
			*  Description: none
			*  Date Added: Fri, Aug 16, 2013
			*/
		  _parse_cake_input_name : function (name) {
			  input = name.match(/data\[(.*?)\]\[(.*?)\]/);
			  return {model:input[1], field:input[2]};
		  }

		})
	})(jQuery)
