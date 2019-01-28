<div class="col-md-12">
	<h4>{__THUMBNAILS_SECTION__}</h4>
	<hr />
	<div class="row form-group">
		<div class="col-md-3">
			<label for="" class="form-control">
				{__THUMBNAILS_AVAILABLE_TITLE__} 
				<a href="#" data-toggle="tooltip" data-placement="right" title="{__THUMBNAILS_AVAILABLE_TOOLTIP__}">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
			</label>
		</div>
		<div class="col-md-9">
			<div class="row">
				{__THUMBNAILS_AVAILABLE__}
				<div class="col-md-3" id="DeleteThumbnailsLoader"></div>
			</div>
		</div>
	</div>
	<form action="#" method="POST" id="thumbnailForm">
		<div class="row form-group">
			<div class="col-md-3">
				<label for="sizeThumbnail" class="form-control">
					{__THUMB_MAX_SIZE__} 
					<a href="#" data-toggle="tooltip" data-placement="right" title="{__THUMB_MAX_SIZE_TOOLTIP__}">
						<span class="glyphicon glyphicon-question-sign"></span>
					</a>
				</label>
			</div>
			<div class="col-md-2">
				<input type="text" id="sizeThumbnails" name="sizeThumbnails" value="200" placeholder="200" class="form-control" />
			</div>
			<div class="col-md-3">
				<input type="button" class="btn btn-default btn-md" id="GenerateThumbnailsButton" value="{__GENERATE_THUMBNAILS__}" />
			</div>
			<div class="col-md-4" id="GenerateThumbnailsLoader"></div>
		</div>
		<div class="row form-group">
			<div class="col-md-4">
				<div id="div-preview-thumbnail-height" class="div-preview-thumbnail"></div>
			</div>
			<div class="col-md-4">
				<div id="div-preview-thumbnail-width" class="div-preview-thumbnail"></div>
			</div>
			<div class="col-md-4">
				<div id="div-preview-thumbnail-both" class="div-preview-thumbnail"></div>
			</div>
		</div>
	</form>
	<form action="#" method="POST" id="updateThumbnailsForm">
		<div class="row form-group">
			<div class="col-md-5">
				<input type="button" class="btn btn-default btn-md" id="regenerateAllThumbnailsButton" value="{__UPDATE_THUMBNAILS__}" />
				<a href="#" data-toggle="tooltip" data-placement="right" title="{__UPDATE_THUMBNAILS_TOOLTIP__}">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
			</div>
			<div class="col-md-4" id="GenerateAllThumbnailsLoader"></div>
		</div>
	</form>
	{__SEARCH_FORM__}
</div>