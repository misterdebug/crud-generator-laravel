@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($post, array('route' => array('posts.update', $post->id), 'method' => 'PUT')) }}

		<div class="form-group">
			{{ Form::label('title', 'Title') }}
			{{ Form::text('title', null, array('class' => 'form-control')) }}
		</div>
		<div class="form-group">
			{{ Form::label('url', 'Url') }}
			{{ Form::text('url', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
