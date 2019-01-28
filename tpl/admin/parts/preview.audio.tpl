<div class="form-group" style="margin: 0;">
	<div id="media_preview">
		<div class="thumbnail hidden">
			<audio controls>
				<source src="{__PATH__}{__mp3__}" type="audio/mpeg" title="{__title__}" class="media mp3" />
			</audio>
			<audio controls>
				<source src="{__PATH__}{__ogg__}" type="audio/ogg" title="{__title__}" class="media ogg" />
			</audio>
			<audio controls>
				<source src="{__PATH__}{__wav__}" type="audio/wav" title="{__title__}" class="media wav" />
			</audio>
			<div class="caption">
				<strong>mp3 : </strong>
				<p class="mp3">{__mp3__}</p>
				<strong>ogg : </strong>
				<p class="ogg">{__ogg__}</p>
				<strong>wav : </strong>
				<p class="wav">{__wav__}</p>
				<strong>title : </strong>
				<p class="media-title">{__title__}</p>
				<strong>desciption : </strong>
				<p class="media-description">{__media_description__}</p>
			</div>
		</div>
	</div>
</div>