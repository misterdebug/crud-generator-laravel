@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ html()->form()->modelform($post, 'PUT', route('posts.update', $post->id)) }}

		<div class="mb-3">
			{{ html()->label('Title', 'title') }}
			{{ html()->text('title', null) }}
		</div>
		<div class="mb-3">
			{{ html()->label('Url', 'url') }}
			{{ html()->text('url', null) }}
		</div>

		{{ html()->submit('Edit') }}

	{{ html()->form()->close() }}
@stop