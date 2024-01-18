@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('posts.create') }}" class="btn btn-info">Create</a></div>

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
						<div class="d-flex gap-2">
                            <a href="{{ route('posts.show', [$post->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('posts.edit', [$post->id]) }}" class="btn btn-primary">Edit</a>
                            {!! html()->form()->open('DELETE',['posts.destroy', $post->id]) !!}
                                {!! html->submit('Delete') !!}
                            {!! html()->form()->close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop