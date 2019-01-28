<h3>{__MEDIA_MANAGER_TITLE__} ({__MEDIA_TYPE__})</h3>
<hr />
<div class="row">
	<div class="col-md-2 form-group">
		<form action="" method="post" id="mediaTypeForm">
			<select name="media_type_id" id="media_type_id" class="form-control">
				{__MEDIA_TYPES_LIST__}
			</select>
			<input type="hidden" name="deleteMediaConfirmText" id="deleteMediaConfirmText" value="{__CONFIRM_DELETE_MEDIA__}" />
			<input type="hidden" name="archiveMediaConfirmText" id="archiveMediaConfirmText" value="{__CONFIRM_ARCHIVE_MEDIA__}" />
			<input type="hidden" name="deleteAllArchivedMediaConfirmText" id="deleteAllArchivedMediaConfirmText" value="{__CONFIRM_DELETE_ALL_MEDIA__}" />
		</form>
	</div>
	<div class="col-md-10 form-group">
		<form action="" method="post">
			<input type="button" class="btn btn-default btn-md" id="addMediaButton" value="{__ADD_MEDIA__}" />
			<input type="button" class="btn btn-default btn-md" id="medias-ops-menu-button" value="{__MEDIA_OPS_MENU__}" />
			<input type="button" class="btn btn-default btn-md" id="manageMediasButton" value="{__MANAGE_MEDIAS__}" />
			<input type="button" class="btn btn-warning btn-md" id="manageArchivedMediasButton" value="{__MANAGE_ARCHIVED_MEDIAS__}" />
			<input type="button" class="btn btn-danger btn-md" id="deleteAllArchivedMediasButton" value="{__DELETE_ALL_ARCHIVED_MEDIA__}" />
		</form>
	</div>
</div>
<div class="row" id="medias-ops-menu">
	{__MEDIA_MENU_CONTENTS__}
</div>
<div class="row">
	<div id="mediasContainer" class="mansonry-grid">
		{__MEDIAS_BOXES__}
	</div>
</div>
<div class="paging-row">
	{__PAGING__}
</div>
