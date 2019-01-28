$('div#ajaxFrame').find('.iframe-box').each(function(){
	$(this).find("iframe").attr('width', '100%');
	$(this).find("iframe").attr('height', '');
});
if(getCurrentPage() === 'medias_manager') {
	if($('input#manageArchivedMediasButton').is(':visible')) {
		$('input#manageMediasButton').hide();
		$('input#deleteAllArchivedMediasButton').hide();
	} else {
		$('input#manageMediasButton').show();
		$('input#deleteAllArchivedMediasButton').show();
	}
}
if(getCurrentPage() === 'medias_update') {
	$('.thumbnail').removeClass('hidden');
}
$('.mansonry-grid').masonry({
	itemSelector: '.media-item-form'
});
$('meta[name=app_current_page]').change(function(){
	if(getCurrentPage() === 'medias_manager') {
		if($('input#manageArchivedMediasButton').is(':visible')) {
			$('input#manageMediasButton').hide();
			$('input#deleteAllArchivedMediasButton').hide();
		} else {
			$('input#manageMediasButton').show();
			$('input#deleteAllArchivedMediasButton').show();
		}
	}
	if(getCurrentPage() === 'medias_update') {
		$('.thumbnail').removeClass('hidden');
	}
	$('div#ajaxFrame').find('.iframe-box').each(function(){
		$(this).find("iframe").attr('width', '100%');
		$(this).find("iframe").attr('height', '');
	});
	var contentImages = $('div#ajaxFrame img, iframe');
    var totalImages = contentImages.length;
    var loadedImages = 0;
    contentImages.each(function(){
        $(this).on('load', function(){
            loadedImages++;
            if(loadedImages === totalImages){
                $('.mansonry-grid').masonry({
					itemSelector: '.media-item-form'
				});
			}
        });
    });
});
$('div#ajaxFrame').on('click', 'input#cancelMediaButton', function() {
	window.history.back(1);
	window.location.reload(true);
	$('meta[name=app_current_page]').trigger('change');
	return true;
});

$('div#ajaxFrame').on('click', 'input#medias-ops-menu-button', function() {
	if($('#medias-ops-menu').is(':hidden')) {
		$('#medias-ops-menu').show('normal');
		$(this).removeClass('btn-default');
		$(this).addClass('btn-primary');
	} else {
		$('#medias-ops-menu').hide('normal');
		$(this).removeClass('btn-primary');
		$(this).addClass('btn-default');
	}
	return false;
});
$('div#ajaxFrame').on('click', 'input#GenerateThumbnailsButton', function() {
	getWaitContents($('div#GenerateThumbnailsLoader'));
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_manager',
			exw_action: 'Medias::generateThumbnails',
			sMaxSize: $('input#sizeThumbnails').val()
		},'medias_manager');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('input#manageMediasButton').hide();
		setHistoryAndMenu('medias_manager');
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button.regenerateThumbnailsButton', function() {
	getWaitContents($('div#GenerateThumbnailsLoader'));
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_manager',
			exw_action: 'Medias::generateThumbnails',
			sMaxSize: $(this).prev().val()
		},'medias_manager');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('input#manageMediasButton').hide();
		setHistoryAndMenu('medias_manager');
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input#regenerateAllThumbnailsButton', function() {
	getWaitContents($('div#GenerateAllThumbnailsLoader'));
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_manager',
			exw_action: 'Medias::generateAllThumbnails'
		},'medias_manager');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('input#manageMediasButton').hide();
		setHistoryAndMenu('medias_manager');
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'button.deleteThumbnailsButton', function() {
	getWaitContents($('div#DeleteThumbnailsLoader'));
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_manager',
			exw_action: 'Medias::deleteThumbnails',
			sSize: $(this).prev().prev().val()
		},'medias_manager');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('input#manageMediasButton').hide();
		setHistoryAndMenu('medias_manager');
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'a#media-translations-link', function() {
	if($('#media-translations-form').is(':hidden')) {
		$('#media-translations-form').show('normal');
	} else {
		$('#media-translations-form').hide('normal');
	}
	return false;
});
$('div#ajaxFrame').on('keyup', 'input#sizeThumbnails', function(e){
	$('.div-preview-thumbnail').css('background-color', 'grey');
	$('.div-preview-thumbnail').css('border', '1px dotted #000');
	$('#div-preview-thumbnail-height').css('height', $('input#sizeThumbnails').val());
	$('#div-preview-thumbnail-height').css('width', $('input#sizeThumbnails').val()/2);
	$('#div-preview-thumbnail-width').css('width', $('input#sizeThumbnails').val());
	$('#div-preview-thumbnail-width').css('height', $('input#sizeThumbnails').val()/2);
	$('#div-preview-thumbnail-both').css('height', $('input#sizeThumbnails').val());
	$('#div-preview-thumbnail-both').css('width', $('input#sizeThumbnails').val());
});
$('div#ajaxFrame').on('click', 'input#manageMediasButton', function(e){
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_manager',
			exw_action: 'Medias::getMediasManagerPage',
			media_type_id: $('#mediaTypeToManage').val() || $('select#media_type_id').val(),
			media_active: 1
		},'medias_manager');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('input#manageMediasButton').hide();
		setHistoryAndMenu('medias_manager');
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input#manageArchivedMediasButton', function(){
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_manager',
			exw_action: 'Medias::getMediasManagerPage',
			media_type_id: $('#mediaTypeToManage').val() || $('select#media_type_id').val(),
			media_active: 0
		},'medias_manager');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('input#manageArchivedMediasButton').hide();
		$('input#medias-ops-menu-button').hide();
		setHistoryAndMenu('medias_manager');
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input#searchMediaButton', function(){
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_manager',
			exw_action: 'Medias::searchMedia',
			media_type_id: $('select#mediaTypeToSearch').val(),
			sKeyword: $('input#mediaKeyword').val()
		},'medias_manager');
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		setHistoryAndMenu('medias_manager');
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.editMedia', function(){
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_update',
			exw_action: 'Medias::getUpdateMediaPage',
			media_id: $(this).prev().val()
		},'medias_update');
	promise.success(function(data) {
		setHistoryAndMenu('medias_update');
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
		$('div#ajaxFrame').find('.thumbnail').removeClass('hidden');
		var src = $('#media_preview').find('.media').attr('src');
		if(src !== 'undefined') {
			$('#media_preview').find('p.file-name').html(src.substring(src.lastIndexOf("/")+1));
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input.deleteMedia, button.deleteMedia', function(){
	var sMediaId = $(this).prev().prev().val();
	var msg = '';
	if($('input#manageArchivedMediasButton').is(':visible')) {
		msg = $('input#archiveMediaConfirmText').val();
	} else {
		msg = $('input#deleteMediaConfirmText').val();
	}
	bootbox.confirm(msg, function(result){
		if(result) {
			var promise = genericRequest({
				app_token: getToken(), 
				content: 'medias_manager',
				exw_action: 'Medias::deleteMedia',
				media_id: sMediaId,
				media_type_id: $('form#mediaTypeForm').find('#media_type_id').val()
			});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
				$('input#manageMediasButton').hide();
				$('meta[name=app_current_page]').trigger('change');
			});
		}
	});
});
$('div#ajaxFrame').on('click', 'input#deleteAllArchivedMediasButton', function(){
	var msg = $('input#deleteAllArchivedMediaConfirmText').val();
	bootbox.confirm(msg, function(result){
		if(result) {
			var promise = genericRequest({
				app_token: getToken(), 
				content: 'medias_manager',
				exw_action: 'Medias::deleteAllArchivedMedia',
				media_type_id: $('form#mediaTypeForm').find('#media_type_id').val()
			});
			promise.success(function(data) {
				$('div#ajaxFrame').html(data);
				$('meta[name=app_current_page]').trigger('change');
			});
		}
	});
});
$('div#ajaxFrame').on('click', 'input.restoreMedia', function(){
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_manager',
			exw_action: 'Medias::restoreMedia',
			media_id: $(this).prev().val(),
			media_type_id: $('form#mediaTypeForm').find('#media_type_id').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('input#manageMediasButton').hide();
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input#addMediaButton', function(){
	var promise = genericRequest({
			app_token: getToken(), 
			content: 'medias_add',
			exw_action: 'Medias::getAddMediaPage',
			media_type_id: $('select#media_type_id').val()
		},'medias_add');
	promise.success(function(data) {
		setHistoryAndMenu('medias_add');
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	return false;
});
$('div#ajaxFrame').on('change', 'select#media_type_id', function(){
	var exw_action = '';
	if(getCurrentPage() === 'medias_add') {
		if (getHasChanges(false) === true) {
			if (!confirm('Êtes vous sûr de ne pas vouloir enregistrer vos modifications ?')) {
				for (var x = 0; x < this.length; x++) {
					this.options[x].selected = this.options[x].defaultSelected;
				}
				return false;
			}
		}
		exw_action = 'Medias::getAddMediaPage';
	} else if(getCurrentPage() === 'medias_manager') {
		exw_action = 'Medias::getMediasManagerPage';
	} else {
		return true;
	}
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: exw_action,
			media_type_id: $('select#media_type_id').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		alert('error');
	});
	return true;
});
$('div#ajaxFrame').on('change', 'input[type="file"]', function() {
	var files = $(this)[0].files;
	var sExt = $(this).attr('name');
	if (files.length > 0) {
		// On part du principe qu'il n'y a qu'un seul fichier
		$.each(files, function(iKey, oFile) {
			$.each(oFile, function(sKey, sVal) {
				if(sKey === 'name') {
					if(sExt !== 'src') {
						sExt = sVal.substring(sVal.lastIndexOf('.')+1);
					}
					if($('.'+sExt)) {
						// Ici on injecte les informations recoltées sur le fichier pour l'utilisateur
						$('.thumbnail').removeClass('hidden');
						$('.'+sExt).each(function(index) {
							var src = window.URL.createObjectURL(files[iKey]);
							$(this).attr('src', src);
							$(this).load();
							src = src.substring(5);
							$('p.file-name').html(src+'<br />'+files[iKey].size +' bytes');
							$('.caption p.media-title').html($("input[name^='title']").val());
						});
					}
				}
			});
		});
	}
});
$('div#ajaxFrame').on('keyup', 'input[type=text], textarea', function(e) {
	e.preventDefault();
	$('.thumbnail').removeClass('hidden');
	if($(this).attr('name').indexOf('iframe') === 0) { // iframe soundcloud
		$(this).val($(this).val().replace(/&amp;/g, '&').replace('%3A', ':'));
	}
	if($(this).attr('name').indexOf('title') === 0) {
		$('.media').attr('title', $(this).val());
		$('p.media-title').html($(this).val());
	}
	if($(this).attr('name').indexOf('alt') === 0) {
		$('p.media-alt').html($(this).val());
	}
	if($(this).attr('name').indexOf('media_description') === 0) {
		$('p.media-description').html($(this).val());
	}
	if($(this).attr('name').indexOf('iframe') === 0) {
		$('div.iframe').html($(this).val());
	}
});
$('div#ajaxFrame').on('click', 'input#saveMediaButton', function(){
	if($('textarea#iframe')) {
		$('textarea#iframe').val(htmlEntities($('textarea#iframe').val()));
	}
	$('#addMediaForm').append('<input type="hidden" name="app_token" value="'+getToken()+'" />');
	$('#addMediaForm').append('<input type="hidden" name="exw_action" value="Medias::addMedia" />');
	$('#addMediaForm').append('<input type="hidden" name="content" value="medias_update" />');
	var formdata = (window.FormData) ? new FormData($('#addMediaForm')[0]) : null;
	$(".progress").css('display', 'block');
	$.ajax({
		type: 'POST',
		cache:'false',
		contentType: false,
		processData: false,
		url: "https://"+window.location.hostname+"/index.php?page=medias_update&lang="+getLang(),
		xhr: function() {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(evt){
				if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					$("#progressbar").attr('aria-valuenow', Math.round(percentComplete*100));
					$("#progressbar").text(Math.round(percentComplete*100)+'%');
					$("#progressbar").css('width', Math.round(percentComplete*100)+'%');
				}
			}, false);
			return xhr;
		},
		data: (formdata !== null) ? formdata : $('#addMediaForm').serialize(),
		success: function(data){
			$('div#ajaxFrame').html(data);
			$('meta[name=app_current_page]').trigger('change');
			setHistoryAndMenu('medias_update');
			$('.thumbnail').removeClass('hidden');
			var src = $('#media_preview').find('img').attr('src');
			$('#media_preview').find('h4').html(src.substring(src.lastIndexOf("/")+1));
			$('meta[name=app_current_page]').trigger('change');
		},
		error:function(){
			alert('error');
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input#updateMediaButton', function(){
	if($('textarea#iframe')) {
		$('textarea#iframe').val(htmlEntities($('textarea#iframe').val()));
	}
	$('#updateMediaForm').append('<input type="hidden" name="exw_action" value="Medias::updateMedia" />');
	$('#updateMediaForm').append('<input type="hidden" name="content" value="medias_update" />');
	var formdata = (window.FormData) ? new FormData($('#updateMediaForm')[0]) : null;
	$('.progress').css('display', 'block');
	$.ajax({
		type: 'POST',
		cache:'false',
		contentType: false,
		processData: false,
		url: "https://"+window.location.hostname+"/index.php?page=medias_update&lang="+getLang(),
		xhr: function() {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(evt){
				if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					$("#progressbar").attr('aria-valuenow', Math.round(percentComplete*100));
					$("#progressbar").text(Math.round(percentComplete*100)+'%');
					$("#progressbar").css('width', Math.round(percentComplete*100)+'%');
				}
			}, false);
			return xhr;
		},
		data: (formdata !== null) ? formdata : $('#updateMediaForm').serialize(),
		success: function(data){
			$('div#ajaxFrame').html(data);
			$('.thumbnail').removeClass('hidden');
			var src = $('#media_preview').find('img').attr('src');
			$('#media_preview').find('h4').html(src.substring(src.lastIndexOf("/")+1));
			$('meta[name=app_current_page]').trigger('change');
		},
		error:function(){
			alert('error');
		}
	});
	return false;
});
$('div#ajaxFrame').on('click', 'input#createDedicatedPageNameButton', function(){
	if($("#dedicatedPageName").val() === '') {
		addMsg('danger', '<li>'+$("#pageNameEmptyMsg").val()+'</li>');
		return false;
	}
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Medias::createDedicatedPage',
			media_type_id: $('input#media_type_id').val(),
			media_id: $('input#media_id').val(),
			dedicatedPageName: $('input#dedicatedPageName').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		alert('error');
	});
	return true;
});
$('div#ajaxFrame').on('click', 'input#updateDedicatedPageNameButton', function(){
	if($("#dedicatedPageName").val() === '') {
		addMsg('danger', '<li>'+$("#pageNameEmptyMsg").val()+'</li>');
		return false;
	}
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Medias::updateDedicatedPage',
			media_type_id: $('input#media_type_id').val(),
			media_id: $('input#media_id').val(),
			dedicatedPageName: $('input#dedicatedPageName').val(),
			mediaCurrentPageName: $('a#mediaCurrentPageName').html()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		alert('error');
	});
	return true;
});
$('div#ajaxFrame').on('click', 'button#deleteDedicatedPageNameButton', function(){
	var promise = genericRequest({
			app_token: getToken(), 
			content: getCurrentPage(),
			exw_action: 'Medias::deleteDedicatedPage',
			media_type_id: $('input#media_type_id').val(),
			media_id: $('input#media_id').val(),
			dedicatedPageName: $('a#mediaCurrentPageName').val()
		});
	promise.success(function(data) {
		$('div#ajaxFrame').html(data);
		$('meta[name=app_current_page]').trigger('change');
	});
	promise.error(function() {
		alert('error');
	});
	return true;
});