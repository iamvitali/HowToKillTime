@if (Request::input('Response') === 'True')
	<div class="panel panel-default">
		<table class="table table-striped">
			@foreach (Request::input('Search') as $film)
				<tr>
					<td>
						Title: {{ $film['Title'] }} @if ($film['Type'] === 'series') (TV Series) @endif
						<br>
						Year: {{ $film['Year'] }}
					</td>
				</tr>
			@endforeach
		</table>
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