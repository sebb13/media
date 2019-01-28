<h3>{__MEDIA_MANAGER__}</h3>
<hr />
<div class="row">
	<div class="col-md-6">
		<form method="POST" action="">
			<fieldset>
				<legend>{__MANAGE_MEDIAS__}</legend>
				<div class="row form-group">
					<div class="col-md-3">
						<select name="mediaTypeToManage" id="mediaTypeToManage" class="form-control">
							{__MEDIA_TYPES_LIST__}
						</select>
					</div>
					<div class="col-md-3">
						<input type="button" class="btn btn-default btn-md" id="manageMediasButton" value="{__MANAGE_MEDIAS__}" />
					</div>
					<div class="col-md-6">
						<input type="button" class="btn btn-warning btn-md" id="manageArchivedMediasButton" value="{__MANAGE_ARCHIVED_MEDIAS__}" />
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="col-md-6">
		{__SEARCH_MEDIA_FORM__}
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<form method="POST" action="">
			<fieldset>
				<legend>{__ADD_MEDIA__}</legend>
				<div class="row form-group">
					<div class="col-md-3">
						<select name="mediaTypeId" id="media_type_id" class="form-control">
							{__MEDIA_TYPES_LIST__}
						</select>
					</div>
					<div class="col-md-4">
						<input type="button" class="btn btn-default btn-md" id="addMediaButton" value="{__ADD_MEDIA__}" />
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="col-md-6">
		<form method="POST" action="">
			<fieldset>
				<legend>{__ACTIVE_MEDIAS__}</legend>
				{__MEDIAS_STATS__}
			</fieldset>
		</form>
	</div>
</div>
<br />
{__CONFIG__}

