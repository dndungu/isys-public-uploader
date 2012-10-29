var isys_public_uploader = {
	init: function(){
		this.initPostSubmit();
	},
	initPostSubmit: function(){
		jQuery('#isys_visitor_post_form').submit(function(event){
			event.preventDefault();
			var subject = jQuery(this);
			jQuery.post(isys_public_uploader_the_ajax_script.ajaxurl, subject.serialize(), function(){
				subject.html('Thank you for the post - a moderator will read it and publish. Click here to return to <a href="javascript:history.back();">'+jQuery('#category_name').val()+'</a>');
			});
		});
	}
};

jQuery(document).ready(function(){
	isys_public_uploader.init();
});