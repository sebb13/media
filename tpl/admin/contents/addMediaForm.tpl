<form action="" method="post" enctype="multipart/form-data" id="addMediaForm">
	<div class="row">
		<div class="col-md-6 form-group">
			<h3>{__ADD_MEDIA__} ({__MEDIA_TYPE__})</h3>
		</div>
		<div class="col-md-3 form-group">
			<select name="media_type_id" id="media_type_id" class="form-control media-select">
				{__MEDIA_TYPES_LIST__}
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 form-group">	
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
				<input type="radio" name="media_download_allowed" value="0" checked />{__NO__}
			</label>
			<label class="radio-inline">
				<input type="radio" name="media_download_allowed" value="1" />{__YES__}
			</label>
			<fieldset>
				<legend>{__CONTROLS__}</legend>
				<div class="row">
					<div class="col-md-5">
						<input type="button" class="btn btn-success" id="saveMediaButton" value="{__SAVE__}" />
						<input type="button" class="btn btn-danger" id="cancelMediaButton" value="{__CANCEL__}" />
					</div>
					<div class="col-md-7">
						<div class="progress" style="display:none">
							<div id="progressbar" class="progress-bar-success progress-bar-striped active text-center" style="width:0">
								0%
							</div>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6 form-group">
			{__EXTENSIONS_ALLOWED_BLOC__}
			{__PREVIEW__}
		</div>
	</div>
</form>