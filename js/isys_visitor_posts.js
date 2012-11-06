var isys_public_uploader = {
	init: function(){
		this.initElements();
		this.initStringTrim();
		this.initMaxPostSize();
		this.initUploadObject();
		this.initPostSubmit();
		this.initFileUpload();
		this.initVoting();
	},
	initStringTrim: function(){
		if(typeof String.trim != 'undefined') return;
		String.prototype.trim = function(){
			return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
		};		
	},
	ajaxObject: false,
	attachments: false,
	progressIndicator: false,
	progressIndicatorParent: false,
	uploadElement: false,
	initElements: function(){
		this.attachments = jQuery('.attachments .field');
		this.progressIndicator = jQuery('.progress');
		this.progressIndicatorParent = this.progressIndicator.parent();
		this.uploadElement = jQuery('#isys_visitor_post_form input[type="file"]');
	},
	settings: {
		source: 'upload.php',
		uploader: 'iframe',
		maxPostSize: 0
	},
	initMaxPostSize: function(){
		var subject = this;
		$.post(isys_public_uploader_the_ajax_script.ajaxurl, {"action": "isys_visitor_plugin", "do": "get-post-maxsize"}, function(){
			subject.settings.maxPostSize = parseInt(arguments[0]);
		});
	},
	initVoting: function(){
		jQuery('.isys_visitor_posts .post-vote').click(function(){
			var subject = $(this);
			var message = {};
			message['vote'] = subject.attr('vote');
			message['action'] = 'isys_visitor_plugin';
			message['do'] = 'post-vote';
			message['postID'] = subject.attr('post');
			jQuery.post(isys_public_uploader_the_ajax_script.ajaxurl, message, function(){
				subject.parent().fadeOut();
				switch(message['vote']){
					case 'up':
						jQuery('.likes-count').html(arguments[0]);
						break;
					case 'down':
						jQuery('.dislikes-count').html(arguments[0]);
						break;
				}
			});
		});
	},
	initPostSubmit: function(){
		jQuery('#isys_visitor_post_form').submit(function(event){
			event.preventDefault();
			tinyMCE.triggerSave();
			var subject = jQuery(this);
			jQuery.post(isys_public_uploader_the_ajax_script.ajaxurl, subject.serialize(), function(){
				var response = jQuery.parseJSON(arguments[0]);
				if(typeof response.error == 'string'){
					jQuery('#recaptcha_response_field').attr('placeholder', response.error).css({border:"1px inset #fb3a3a"});
					jQuery('#recaptcha_response_field').val('');
				}else {
					subject.html('Thank you for the post - a moderator will read it and publish. Click here to return to <a href="javascript:history.back();">'+jQuery('#category_name').val()+'</a>');
				}
			});
		});
		jQuery('#isys_visitor_comment_form').submit(function(event){
			event.preventDefault();
			var subject = jQuery(this);
			jQuery.post(isys_public_uploader_the_ajax_script.ajaxurl, subject.serialize(), function(){
				var response = jQuery.parseJSON(arguments[0]);
				if(typeof response.error == 'string'){
					jQuery('#recaptcha_response_field').attr('placeholder', response.error).css({border:"1px inset #fb3a3a"});
					jQuery('#recaptcha_response_field').val('');
				}else {
					subject.html('Thank you for the comment - a moderator will read it and publish.</a>');
				}
			});			
		});
	},
	initUploadObject: function(){
		var subject = this;
		subject.ajaxObject = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
		if(typeof this.ajaxObject.upload != 'object') return;
		subject.settings.uploader = 'ajax';
		subject.ajaxObject.upload.addEventListener('loadstart', subject.onLoadStart);
		subject.ajaxObject.upload.addEventListener('loadend', subject.onLoadEnd);
		subject.ajaxObject.upload.addEventListener('progress', subject.onProgress);
		subject.ajaxObject.upload.addEventListener('progress', subject.onProgress);
		subject.ajaxObject.onreadystatechange = function(){
			subject.onComplete(subject.ajaxObject);
		};		
	},	
	initFileUpload: function(){
		var subject = this;
		subject.uploadElement.change(function(){
			switch(subject.settings.uploader){
				case 'ajax':
					subject.ajaxUpload(arguments[0]);
					break;
				case 'iframe':
					console.info('iframe');
					break;
			}
		});
	},
	ajaxUpload: function(){
		var subject = this;
		var event = arguments[0];
		var data = new FormData();
		data.append('action', 'isys_visitor_plugin');
		data.append('do', 'upload-pdf');
		var files = event.currentTarget.files;
		for(i in files){
			var file = files[i];
			if(!(file instanceof File)) continue;
			if(file.size > subject.settings.maxPostSize){
				subject.writeError('Please select a file with less than ' + (subject.settings.maxPostSize/1048576) + 'Mb.');
				return;
			}
			if(file.type.trim() != 'application/pdf'){
				subject.writeError('Please upload PDF files only.');
				return;
			}
			data.append(file.name, file);
		}
		subject.ajaxObject.open("POST", isys_public_uploader_the_ajax_script.ajaxurl);
		subject.ajaxObject.setRequestHeader("Cache-Control", "no-cache");
		subject.ajaxObject.send(data);
	},
	writeError: function(){
		this.writeBox($('.errorBox'), arguments[0]);
	},
	writeBox: function(){
		var subject = arguments[0];
		subject.css({display: 'none'});
		subject.html(arguments[1]);
		subject.fadeIn(function(){
			setTimeout(function(){
				subject.fadeOut();
			}, 15000);
		});		
	},
	onLoadStart: function(){
		subject.progressIndicatorParent.css({display: 'inline-block'});
	},
	onLoadEnd: function(){
		subject.progressIndicatorParent.css({display: 'none'});
	},
	onProgress: function(){
		return;
		var subject = isys_public_uploader;
		var event = arguments[0];
		if(!event.lengthComputable) return;
		var parentWidth = subject.progressIndicatorParent.width();
		var width = Math.floor(((event.position / event.totalSize) * parentWidth)) - 2;
		var widthPercentage = Math.ceil((width/parentWidth) * 100);
		subject.progressIndicator.width(width).html((widthPercentage + '%'));
	},
	onComplete: function(){
		var subject = this;
		if(arguments[0].readyState != 4 || arguments[0].status != 200) return;
		subject.progressIndicator.html('').width(0);
		var response = jQuery.parseJSON(arguments[0].responseText);
		for(i in response){
			var attachment = response[i];
			subject.attachments.prepend('<span style="width:100%;"><input type="hidden" name="attachments['+attachment.ID+']" value="'+attachment.name+'"/>'+attachment.name+' <a href="javascript:isys_public_uploader.removeUpload('+attachment.ID+')">remove</a></span>');
		}
		subject.uploadElement.val('');
	},
	removeUpload: function(){
		jQuery('input[name="attachments[' + arguments[0] + ']"]').parent().remove();
	}
};

jQuery(document).ready(function(){
	isys_public_uploader.init();
});