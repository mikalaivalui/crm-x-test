<head>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
@include('Importer::layouts.menu')
<h1>@yield('title')</h1>

@yield('content');


</div>
</body>
