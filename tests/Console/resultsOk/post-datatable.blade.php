<div>
    <div class="col-md-4 mt-4 mb-4">
        <div class="input-group">
            <input type="text" class="form-control" wire:model.live.debounce.500ms="search" placeholder="Search">
            <span class="input-group-text">
                🔍
            </span>
        </div>
    </div>
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
					<td>{!! $this->search ? $this->highlightTitle($post->title) : $post->title !!}</td>
					<td>{{ $post->url }}</td>

                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('posts.show', [$post->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('posts.edit', [$post->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['posts.destroy', $post->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $posts->links() }}
</div>
