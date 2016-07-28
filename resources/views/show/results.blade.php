<div class="panel panel-default">
	<table class="table table-striped">
		@foreach (Request::input('Search') as $film)
			<tr>
				<td>
					Title: {{ $film['Title'] }}
					<br>
					Year: {{ $film['Year'] }}
					<br>
					Type: {{ $film['Type'] }}
				</td>
			</tr>
		@endforeach
	</table>
</div>