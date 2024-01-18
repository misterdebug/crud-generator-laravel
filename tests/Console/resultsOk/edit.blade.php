@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ html()->form()->modelform($post, 'PUT', array('route' => array('posts.update', $post->id))) }}

		<div class="mb-3">
			{{ html()->form()->label('title', 'Title') }}
			{{ html()->form()->text('title', null) }}
		</div>
		<div class="mb-3">
			{{ html()->form()->label('url', 'Url') }}
			{{ html()->form()->text('url', null) }}
		</div>

		{{ html()->form()->submit('Edit') }}

	{{ html()->form()->close() }}
@stop