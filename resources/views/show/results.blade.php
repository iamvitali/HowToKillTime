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
						<span style='padding-left: 10px;'>
							{{ $film['Title'] }}  @if ($film['Type'] === 'series') (TV Series) @endif
							<span class='pull-right'>
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
							Actors: <span class="film-info-actors"></span><br>
							Awards: <span class="film-info-awards"></span><br>
							Box Office: <span class="film-info-box-office"></span><br>
							Country: <span class="film-info-country"></span><br>
							DVD: <span class="film-info-dvd"></span><br>
							Director: <span class="film-info-director"></span><br>
							Genre: <span class="film-info-genre"></span><br>
							Language: <span class="film-info-language"></span><br>
							Metascore: <span class="film-info-metascore"></span><br>
							Plot: <span class="film-info-plot"></span><br>
							Production: <span class="film-info-production"></span><br>
							Rated: <span class="film-info-rated"></span><br>
							Released: <span class="film-info-released"></span><br>
							Runtime: <span class="film-info-runtime"></span><br>
							Website: <span class="film-info-website"></span><br>
							Writer: <span class="film-info-writer"></span><br>
							<hr>
							Rating: <span class="film-info-imdb-rating"></span><br>
							IMDb Votes: <span class="film-info-imdb-votes"></span><br>
							Tomato Consensus: <span class="film-info-tomato-consensus"></span><br>
							Tomato Fresh: <span class="film-info-tomato-fresh"></span><br>
							Tomato Image: <span class="film-info-tomato-image"></span><br>
							Tomato Meter: <span class="film-info-tomato-meter"></span><br>
							Tomato Rating: <span class="film-info-tomato-rating"></span><br>
							Tomato Reviews: <span class="film-info-tomato-reviews"></span><br>
							Tomato Rotten: <span class="film-info-tomato-rotten"></span><br>
							Tomato URL: <span class="film-info-tomato-url"></span><br>
							Tomato User Metter: <span class="film-info-tomato-user-meter"></span><br>
							Tomato Rating: <span class="film-info-tomato-user-rating"></span><br>
							Tomato Revoews: <span class="film-info-tomato-user-reviews"></span><br>
							Tomato Seasons: <span class="film-info-seasons"></span>
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