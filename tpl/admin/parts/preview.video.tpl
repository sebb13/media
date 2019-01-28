<div class="form-group" style="margin: 0;">
	<div id="media_preview">
		<div class="thumbnail hidden">
			<video controls poster="{__poster__}">
				<source src="{__PATH__}{__mp4__}" type="audio/mpeg" title="{__title__}" class="media mp3" />
			</video>
			<video controls poster="{__poster__}">
				<source src="{__PATH__}{__ogg__}" type="audio/ogg" title="{__title__}" class="media ogg" />
			</video>
			<video controls poster="{__poster__}">
				<source src="{__PATH__}{__webm__}" type="audio/webm" title="{__title__}" class="media webm" />
			</video>
			<div class="caption">
				<strong>mp4 : </strong>
				<p class="mp4">{__mp4__}</p>
				<strong>ogg : </strong>
				<p class="ogg">{__ogg__}</p>
				<strong>webm : </strong>
				<p class="webm">{__webm__}</p>
				<strong>title : </strong>
				<p class="media-title">{__title__}</p>
				<strong>desciption : </strong>
				<p class="media-description">{__media_description__}</p>
			</div>
		</div>
	</div>
</div>