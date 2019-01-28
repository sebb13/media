<div class="form-group col-md-4 media-item-form">
	<div class="panel panel-default conf-box">
		<div class="panel-heading">
			<div class="caption">
				<p>title : {__title__}</p>
				<p>descitpion : {__media_description__}</p>
			</div>
			<div class="panel-body media-item-contents">
				<div class="thumbnail">
					<video width="100%" controls poster="{__poster__}">
						<source src="{__PATH__}{__mp4__}" type="audio/mpeg" title="{__title__}">
						<source src="{__PATH__}{__ogg__}" type="audio/ogg" title="{__title__}">
						<source src="{__PATH__}{__webm__}" type="audio/webm" title="{__title__}">
					</video>
				</div>
			</div>
			<div class="panel-footer media-footer-box text-center">
				{__CONTROLS__}
			</div>
		</div>
	</div>
</div>