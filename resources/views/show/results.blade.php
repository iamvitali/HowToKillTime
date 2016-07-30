@if (Request::input('Response') === 'True')
	<div id="searchResults" class="panel-group">
		@foreach (Request::input('Search') as $film)
			<div
				class="panel panel-default collapsed"
				data-toggle="collapse"
				data-parent="#searchResults"
				href="#{{ $film['imdbID'] }}"
				data-imdb-id="{{ $film['imdbID'] }}"
				aria-expanded="false"
			>
				<a>
					<div class="panel-heading">
						<span style="padding-left: 10px;">
							{{ $film['Title'] }}  @if ($film['Type'] === 'series') (TV Series) @endif
							<span class="pull-right">
								{{ $film['Year'] }}
							</span>
						</span>

					</div>
				</a>

				<div
					id="{{ $film['imdbID'] }}"
					class="panel-collapse collapse"
				>
					<div class="panel-body">
						<div class="film-info" style="visibility: hidden;">
							<div class="progress">
								<div class="progress-bar film-info-imdb-rating-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="10" style="width: 0%;">
									Rating: <span class="film-info-imdb-rating"></span> (voted: <span class="film-info-imdb-votes"></span>)
								</div>
							</div>

							<div class="text-center">
								<span class="film-info-rated"></span> | <span class="film-info-runtime"></span> | <span class="film-info-genre"></span> | <span class="film-info-released"></span>
								<span class="film-trophies" style="display: none;"><br><span class="fa fa-trophy" aria-hidden="true"></span> <span class="film-info-awards"></span></span>
							</div>
							<hr>
							Plot: <span class="film-info-plot"></span><br>
							<hr>

							<h4>Details</h4>

							Actors: <span class="film-info-actors"></span><br>
							Director: <span class="film-info-director"></span><br>
							Writer: <span class="film-info-writer"></span><br>
							Production: <span class="film-info-production"></span><br>

							Language: <span class="film-info-language"></span><br>
							Country: <span class="film-info-country"></span><br>

							Box Office: <span class="film-info-box-office"></span><br>
							DVD: <span class="film-info-dvd"></span><br>

							Metascore: <span class="film-info-metascore"></span><br>

							Website: <span class="film-info-website"></span><br>

							<br>

							<div class="vegetable-section" style="display: none;">
								<h4><a class="film-info-tomato-url" target="_blank">Rotten Tomatoes</a></h4>
								Tomato Consensus: <span class="film-info-tomato-consensus"></span><br>
								Tomato Fresh: <span class="film-info-tomato-fresh"></span><br>
								Tomato Image: <span class="film-info-tomato-image"></span><br>
								Tomato Meter: <span class="film-info-tomato-meter"></span><br>
								Tomato Rating: <span class="film-info-tomato-rating"></span><br>
								Tomato Reviews: <span class="film-info-tomato-reviews"></span><br>
								Tomato Rotten: <span class="film-info-tomato-rotten"></span><br>
								Tomato User Metter: <span class="film-info-tomato-user-meter"></span><br>
								Tomato Rating: <span class="film-info-tomato-user-rating"></span><br>
								Tomato Revoews: <span class="film-info-tomato-user-reviews"></span><br>
								Tomato Seasons: <span class="film-info-seasons"></span>
							</div>
						</div>
					</div>
				</div>

			</div>
		@endforeach
		</div>
	</div>
@elseif (Request::input('Response') === 'False' && Request::input('Error') === 'Movie not found!')
	<div class="alert alert-info text-center" role="alert">
		Nothing found.
		<br>
		<br>
		Try looking for "Batman"!
	</div>
@elseif (Request::input('Response') === 'False' && Request::input('Error') === 'Must provide more than one character.')
	<div class="alert alert-warning text-center" role="alert">
		Oh, there are so many films that start with that letter! Maybe add one more?
	</div>
@elseif (Request::input('Response') === 'False')
	<div class="alert alert-warning text-center" role="alert">
		{{-- Displaying the error that was returned by OMDb API: --}}
		{{ Request::input('Error') }}
	</div>
@else
	<div class="alert alert-danger text-center" role="alert">
		Wow this is embarrassing but our servers went bananas. Maybe try again? Please?
	</div>
@endif