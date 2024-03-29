<?php





use Illuminate\Support\Facades\Route;

// Render app page
use Illuminate\Broadcasting\BroadcastController;


Route::get('/', function(){
	return view('start');
});

//$router->get('/', [\App\Http\Controllers\StartController::class, 'getApp']);
$router->get('/old', [\App\Http\Controllers\StartController::class, 'getAppOld']);
$router->get('/test', [\App\Http\Controllers\StartController::class, 'getTest']);

$modules = [
    'Address',
    'CustomerSettings',
    'History',
    'Importer',
    'Invoice',
    'Person',
    'Service',
    'Type',
    'User',
    'UserSettings',
    'WorkOrder',
];

$path = realpath(__DIR__ . '/../app/Modules/');


foreach ($modules as $module) {
    require realpath($path . '/' . $module . '/Http/routes.php');
}
