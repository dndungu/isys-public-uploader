<?php print '<?xml version="1.0" encoding="UTF-8" ?>'?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
<style type="text/css">
	body, form {
		padding:0;
		margin:0;
		background-color:transparent;
	}
</style>
</head>
<body>
	<form enctype="multipart/form-data" id="iframe_uploader" method="post" action="/wp-admin/admin-ajax.php">
		<input type="hidden" name="action" value="isys_visitor_plugin"/>
		<input type="hidden" name="do" value="iframe-upload-pdf"/>
		<input type="file" name="file_upload" id="attachmentFiles" onchange="javascript:document.getElementById('iframe_uploader').submit()"/>
	</form>
	<script src="/wp-includes/js/jquery/jquery.js?ver=1.7.2"></script>
	<script type="text/javascript">
		var isys_uploads = <?php print isset($isys_uploads) ? json_encode($isys_uploads) : '{}'?>;
		var attachments = jQuery('.attachments .field', parent.document);
		for(i in isys_uploads){
			var attachment = isys_uploads[i];
			attachments.prepend('<span style="width:100%;"><input type="hidden" name="attachments['+attachment.ID+']" value="'+attachment.name+'"/>'+attachment.name+' <a href="javascript:isys_public_uploader.removeUpload('+attachment.ID+')">remove</a></span>');
		}
		jQuery('#iframe_uploader').submit(function(){
			var width = 0;
			var progressIndicator = jQuery('.progress', parent.document);
			var totalWidth = progressIndicator.parent().width();
			setInterval(function(){
				width += 10;
				if(width > totalWidth) {
					width = 10;
				}
				var percentageWidth = ((width / totalWidth) * 100);
				progressIndicator.width(width).html(percentageWidth + '%');
			}, 500);
			
		});
	</script>
</body>
</html>