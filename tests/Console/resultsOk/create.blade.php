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
			{{ html->label('title', 'Title') }}
			{{ html->text('title', null) }}
		</div>
		<div class="mb-3">
			{{ html->label('url', 'Url') }}
			{{ html->text('url', null) }}
		</div>


		{{ html->submit('Create') }}

	{{ html()->form()->close() }}


@stop