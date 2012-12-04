var isys_public_uploader = {
	init: function(){
		this.initElements();
		this.initStringTrim();
		this.initMaxPostSize();
		this.initUploadObject();
		this.initPostSubmit();
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
				subject.fadeOut();
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
			switch(isys_public_uploader.settings.uploader){
				case 'ajax':
					isys_public_uploader.ajaxUpload();
					break;
				case 'iframe':
					isys_public_uploader.postArticle();
					break;
			}
		});
		jQuery('#isys_visitor_comment_form').submit(function(event){
			event.preventDefault();
			var subject = jQuery(this);
			jQuery.post(isys_public_uploader_the_ajax_script.ajaxurl, subject.serialize(), function(){
				var response = jQuery.parseJSON(arguments[0]);
				if(typeof response.error == 'string'){
					isys_public_uploader.showInvalidCredentials(response.error);
				}else {
					subject.html(isys_visitor_posts_locale['comment-thanks']);
				}
			});			
		});
	},
	postArticle: function(){
		var subject = jQuery('#isys_visitor_post_form');
		jQuery.post(isys_public_uploader_the_ajax_script.ajaxurl, subject.serialize(), function(){
			var response = jQuery.parseJSON(arguments[0]);
			if(typeof response.error == 'string'){
				isys_public_uploader.showInvalidCredentials(response.error);
			}else {
				tinyMCE.execCommand('mceRemoveControl',false, tinymce.editors[0].id);
				subject.html(isys_visitor_posts_locale['post-thanks'] + '<a href="javascript:history.back();">'+jQuery('#category_name').val()+'</a>');
			}
		});		
	},
	showInvalidCredentials: function(){
		var message = arguments[0];
		$('#author_username').css({"border-color": "#fb3a3a"}).attr('placeholder', message).val('');
		$('#author_password').css({"border-color": "#fb3a3a"}).val('');
	},
	initUploadObject: function(){
		var subject = this;
		subject.ajaxObject = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
		if(typeof this.ajaxObject.upload != 'object') return;
		subject.settings.uploader = 'ajax';
		subject.ajaxObject.upload.addEventListener('loadstart', subject.onLoadStart);
		subject.ajaxObject.upload.addEventListener('loadend', subject.onLoadEnd);
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
					subject.iframeUpload(arguments[0]);
					break;
			}
		});
	},
	ajaxUpload: function(){
		var subject = this;
		var data = new FormData();
		data.append('action', 'isys_visitor_plugin');
		data.append('do', 'upload-pdf');
		var files = document.getElementById('attachmentFiles').files;
		if(!files.length) {
			isys_public_uploader.postArticle();
		}
		for(i in files){
			var file = files[i];
			if(!(file instanceof File)) continue;
			if(file.size > subject.settings.maxPostSize || file.size > 10485760){
				var maxSize = subject.settings.maxPostSize > 10485760 ? 10485760 : subject.settings.maxPostSize;
				subject.writeError('Please select a file with less than ' + (maxSize/1048576) + 'Mb.');
				subject.uploadElement.val('');
				return;
			}
			if(file.type.trim() != 'application/pdf'){
				subject.writeError('Please upload PDF files only.');
				subject.uploadElement.val('');
				return;
			}
			data.append(file.name, file);
		}
		subject.ajaxObject.open("POST", isys_public_uploader_the_ajax_script.ajaxurl);
		subject.ajaxObject.setRequestHeader("Cache-Control", "no-cache");
		subject.ajaxObject.send(data);
	},
	iframeUpload: function(){
		isys_public_uploader.postArticle();
	},
	writeError: function(){
		this.writeBox(jQuery('.errorBox'), arguments[0]);
	},
	writeBox: function(){
		var subject = arguments[0];
		subject.hide();
		subject.html(arguments[1]);
		subject.fadeIn(function(){
			setTimeout(function(){
				subject.html('').fadeOut();
			}, 15000);
		});		
	},
	onLoadStart: function(){
		
	},
	onLoadEnd: function(){
		isys_public_uploader.progressIndicator.html('').width(0);
		isys_public_uploader.uploadElement.val('');
	},
	onProgress: function(){
		var subject = isys_public_uploader;
		var event = arguments[0];
		if(!event.lengthComputable) return;
		var parentWidth = subject.progressIndicatorParent.width();
		var width = Math.floor(((event.position / event.totalSize) * parentWidth)) - 2;
		var widthPercentage = Math.ceil((width/parentWidth) * 100);
		subject.progressIndicator.width(width).html((widthPercentage + '%'));
	},
	onComplete: function(){
		var subject = isys_public_uploader;
		if(arguments[0].readyState != 4 || arguments[0].status != 200) return;
		var response = jQuery.parseJSON(arguments[0].responseText);
		for(i in response){
			var attachment = response[i];
			subject.attachments.prepend('<span style="width:100%;"><input type="hidden" name="attachments['+attachment.ID+']" value="'+attachment.name+'"/>'+attachment.name+' <a href="javascript:isys_public_uploader.removeUpload('+attachment.ID+')">remove</a></span>');
		}
		isys_public_uploader.postArticle();
	},
	removeUpload: function(){
		jQuery('input[name="attachments[' + arguments[0] + ']"]').parent().remove();
	},
	poorProgressIndicator: function(){
		var width = 0;
		var progressIndicator = jQuery('#progress_indicator');
		var totalWidth = progressIndicator.parent().width();
		isys_public_uploader.poorProgressInterval = setInterval(function(){
			width += 10;
			if(width > totalWidth) {
				clearInterval(isys_public_uploader.poorProgressInterval);
				isys_public_uploader.onLoadEnd();
			}
			var percentageWidth = Math.floor((width / totalWidth) * 100);
			progressIndicator.width(width).html(percentageWidth + '%');
		}, 250);		
	},
	poorProgressInterval: false
};

jQuery(document).ready(function(){
	isys_public_uploader.init();
});