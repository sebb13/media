<h3>{__UPDATE_MEDIA__} (id:{__MEDIA_ID__} - {__MEDIA_TYPE__})</h3>
<div class="row">
	<div class="col-md-6 form-group">
		<form action="" method="post" enctype="multipart/form-data" id="updateMediaForm">
			<input type="hidden" name="mediaTypeToManage" id="mediaTypeToManage" value="{__MEDIA_TYPE_ID__}" />
			{__CONTENTS_FORM__}
			<a href="#" id="media-translations-link">{__MEDIA_TRANSLATIONS__}</a>
			<div id="media-translations-form">
				{__TRANSLATIONS_FORM__}
			</div>
			<p class="download-allowed-label">{__DOWNLOAD_ALLOWED__}
				<a href="#" data-toggle="tooltip" data-placement="right" title="{__DOWNLOAD_ALLOWED_TOOLTIP__}">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
			</p>
			<label class="radio-inline">
				<input type="radio" name="media_download_allowed" value="0" {__CHECKED_NO__} />{__NO__}
			</label>
			<label class="radio-inline">
				<input type="radio" name="media_download_allowed" value="1" {__CHECKED_YES__} />{__YES__}
			</label>
			<fieldset>
				<legend>{__CONTROLS__}</legend>
				<div class="row">
					<div class="col-md-7">
						<input type="hidden" name="media_id" id="media_id" value="{__MEDIA_ID__}" />
						<input type="hidden" name="media_type_id" id="media_type_id" value="{__MEDIA_TYPE_ID__}" />
						<input type="button" class="btn btn-success btn-sm" id="updateMediaButton" value="{__SAVE__}" />
						<input type="button" class="btn btn-danger btn-sm" id="cancelMediaButton" value="{__CANCEL__}" />
						<input type="button" class="btn btn-default btn-sm" id="manageMediasButton" value="{__MANAGE_MEDIAS__}" />
					</div>
					<div class="col-md-5">
						<div class="progress" style="display:none">
							<div id="progressbar" class="progress-bar-success progress-bar-striped active text-center" style="width:0">
								0%
							</div>
						</div>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	<div class="col-md-6">
		{__EXTENSIONS_ALLOWED_BLOC__}
		<div class="preview">
			{__PREVIEW__}
		</div>
		{__DEDICATED_PAGE_FORM__}
	</div>
</div>