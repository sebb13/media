<form action="" method="post" enctype="multipart/form-data" id="addPageMediaForm">
	<fieldset>
		<legend>
			{__CREATE_DEDICATED_PAGE__}
			<a href="#" data-toggle="tooltip" data-placement="right" title="{__CREATE_DEDICATED_PAGE_TOOLTIP__}">
				<span class="glyphicon glyphicon-question-sign"></span>
			</a>
		</legend>
		<div class="row">
			<div class="col-md-8">
				<input type="text" id="dedicatedPageName" name="dedicatedPageName" class="form-control input-md" placeholder="{__PAGE_NAME__}">
				<input type="hidden" name="media_id" id="media_id" value="{__MEDIA_ID__}" />
				<input type="hidden" name="media_type_id" id="media_type_id" value="{__MEDIA_TYPE_ID__}" />
				<input type="hidden" id="pageNameEmptyMsg" value="{__ERROR_PAGE_NAME_EMPTY__}" />
			</div>
			<div class="col-md-4">
				<input type="button" class="btn btn-success btn-sm" id="createDedicatedPageNameButton" value="{__CREATE_PAGE__}" />
			</div>
		</div>
	</fieldset>
</form>