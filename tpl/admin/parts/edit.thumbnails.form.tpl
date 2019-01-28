<div class="col-md-3">
	<form method="POST" class="editThumbnailsForm">
		{__THUMBNAILS_SIZE__}px - <span class="{__COMPLETE_CLASS__}">{__COMPLETION_PERCENT__}</span>
		<input type="hidden" name="ThumbnailsSizeToDelete" value="{__THUMBNAILS_SIZE__}" />
		<button type="button" class="btn btn-success btn-sm regenerateThumbnailsButton">
			<span class="glyphicon glyphicon glyphicon-refresh"></span>
		</button>
		<button type="button" class="btn btn-danger btn-sm deleteThumbnailsButton">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
	</form>
</div>