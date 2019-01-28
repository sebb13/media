<form method="POST" action="" id="searchForm">
	<fieldset>
		<legend>{__SEARCH_MEDIA__}</legend>
		<div class="row form-group">
			<div class="col-md-3">
				<select name="mediaTypeToSearch" id="mediaTypeToSearch" class="form-control">
					{__MEDIA_TYPES_LIST__}
				</select>
			</div>
			<div class="col-md-6">
				<input type="text" id="mediaKeyword" name="mediaKeyword" value="" class="form-control" />
			</div>
			<div class="col-md-3">
				<input type="submit" class="btn btn-default btn-md" id="searchMediaButton" value="{__SEARCH__}" />
			</div>
		</div>
	</fieldset>
</form>