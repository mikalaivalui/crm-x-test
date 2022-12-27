@extends('Importer::layouts.template');
@section('title')
Results import
@endsection
@section('content')


<form action="{{ route('send-file')}}" method="post" enctype="multipart/form-data">
	@csrf
	<div class="form-group">
		<input type="file" name="file" class="form-control">
		<button type="submit" class="btn btn-success">Send</button>
	</div>
</form>


@endsection

