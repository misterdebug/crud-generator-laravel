@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! html()->form()->open('posts.store') !!}

		<div class="mb-3">
			{{ html()->form()->label('title', 'Title') }}
			{{ html()->form()->text('title', null) }}
		</div>
		<div class="mb-3">
			{{ html()->form()->label('url', 'Url') }}
			{{ html()->form()->text('url', null) }}
		</div>


		{{ html()->form()->submit('Create') }}

	{{ html()->form()->close() }}


@stop