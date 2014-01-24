// This file handles the inline uploading of images
// This file depends on jquery.filedrop.js

$(function(){

    var dropbox = $('#dropbox'),
        message = $('.message', dropbox);

    dropbox.filedrop({
        // The name of the $_FILES entry:
        paramname:'data[Attachment][img]',

        maxfiles: 40,
    	maxfilesize: 15, // in mb
		url: '/admin/mrg_admin_uploader/attachments/multifile_ajax_upload/'+attachment_foreign_key+'/'+attachment_foreign_model,
        //url: '/admin/project_images/upload/'+project_id,

        uploadFinished:function(i,file,response){
			if (response.status) {
				$.data(file).addClass('done');
			}else{
				$.data(file).addClass('error');
			}

			display_url(file, response);
			$(window).trigger('finished_upload');
            // response is the JSON object that post_file.php returns
        },

    	error: function(err, file) {
            switch(err) {
                case 'BrowserNotSupported':
                    showMessage('Your browser does not support HTML5 file uploads!');
                    break;
                case 'TooManyFiles':
                    alert('Too many files! Please select 5 at most!');
                    break;
                case 'FileTooLarge':
                    alert(file.name+' is too large! Please upload files up to 2mb.');
                    break;
                default:
                    break;
            }
        },

        // Called before each upload is started
        beforeEach: function(file){
            if(!file.type.match(/^image\//)){
                alert('Only images are allowed!');

                // Returning false will cause the
                // file to be rejected
                return false;
            }
        },

        uploadStarted:function(i, file, len){
            createImage(file);
        },

        progressUpdated: function(i, file, progress) {
            $.data(file).find('.progress').width(progress);
        }

    });

	var template = '<div class="preview">'+
                        '<span class="imageHolder">'+
                            '<img />'+
                            '<span class="uploaded"></span>'+
                        '</span>'+
                        '<div class="progressHolder">'+
                            '<div class="progress"></div>'+
                        '</div>'+
                    '</div>';

    function createImage(file){

        var preview = $(template),
            image = $('img', preview);

        var reader = new FileReader();

        image.width = 100;
        image.height = 100;

        reader.onload = function(e){

            // e.target.result holds the DataURL which
            // can be used as a source of the image:

            image.attr('src',e.target.result);
        };

        // Reading the file as a DataURL. When finished,
        // this will trigger the onload function above:
        reader.readAsDataURL(file);

        message.hide();
        preview.appendTo(dropbox);

        // Associating a preview container
        // with the file, using jQuery's $.data():

        $.data(file,preview);
    }

	function showMessage(msg){
        message.html(msg);
    }

	function display_url(file, response) {
		$.data(file).find('.progressHolder').replaceWith('<p>'+response.message+'</p>');
	}
});
