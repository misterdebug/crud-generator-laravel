@extends('default')

@section('content')

	<div class="pull-right"><a href="{{ route('posts.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>title</th>
				<th>url</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($posts as $post)

				<tr>
					<td>{{ $post->id }}</td>
					<td>{{ $post->title }}</td>
					<td>{{ $post->url }}</td>

					<td>
						<a href="{{ route('posts.show', [$post->id]) }}" class="btn btn-info">Show</a>
						<a href="{{ route('posts.edit', [$post->id]) }}" class="btn btn-primary">Edit</a>
						{!! Form::open(['method' => 'DELETE','route' => ['posts.destroy', $post->id]]) !!}
			            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
			        {!! Form::close() !!}
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
