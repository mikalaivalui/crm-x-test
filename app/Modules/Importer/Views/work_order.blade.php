@extends('Importer::layouts.template');
@section('title')
Results import
@endsection
@section('content')




<div class="container">
	<table class="table">
		<tr>
			<th>Ticket</th>
			<th>Entity ID</th>
			<th>Urgancy</th>
			<th>Rcvd Date</th>
			<th>Category</th>
			<th>Store Name</th>
		</tr>
	@foreach ($work_order as $item)
		<tr>
			<td>{{ $item->work_order_number }}</td>
			<td>{{ $item->external_id }}</td>
			<td>{{ $item->priority }}</td>
			<td>{{ $item->received_date }}</td>
			<td>{{ $item->category }}</td>
			<td>{{ $item->fin_loc }}</td>
			<td></td>
		</tr>
	@endforeach
	</table>
</div>

{{ $work_order->links() }}

@endsection
