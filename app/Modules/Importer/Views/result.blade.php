@extends('Importer::layouts.template');
@section('title')
Results import
@endsection
@section('content')
<div class="container">
	<table class="table">
		<tr>
			<th>Type</th>
			<th>Run at</th>
			<th>Entries skipped</th>
			<th>Entries created</th>
			<th>Action</th>
		</tr>
		@foreach ($importer_log as $item)
		<tr>
			<td>{{ $item->type }}</td>
			<td>{{ $item->run_at }}</td>
			<td>{{ $item->entries_processed }}
				@if($item->entries_processed>0)<br>

				List tikets(
				@foreach( $item->entries_processed_arr as $arr_item)
				{{ $arr_item['work_order_number'] }}
				@endforeach)
				@endif
			</td>
			<td>{{ $item->entries_created }}
				@if($item->entries_created>0)<br>
				List tikets(
				@foreach( $item->entries_created_arr as $arr_item)
				{{ $arr_item['work_order_number'] }}
				@endforeach)
				@endif
			</td>
			<td><a target="_blank" href="/importer/{{ $item->id }}/csv">Get CSV</a></td>
		</tr>
		@endforeach
	</table>
</div>

{{ $importer_log->links() }}

@endsection
