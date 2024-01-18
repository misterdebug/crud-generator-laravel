@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! html()->form('POST', route('posts.store'))->open() !!}

		<div class="mb-3">
			{{ html()->label('Title', 'title') }}
			{{ html()->text('title', null) }}
		</div>
		<div class="mb-3">
			{{ html()->label('Url', 'url') }}
			{{ html()->text('url', null) }}
		</div>


		{{ html()->submit('Create') }}

	{{ html()->form()->close() }}


@stop