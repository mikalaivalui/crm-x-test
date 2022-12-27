<?php

use Illuminate\Http\Request;

$router->get('/importer',	[\App\Modules\Importer\Http\Сontrollers\ImporterController::class, 'index'])->name("importer.index");

$router->post('/importer/sendfile',	[\App\Modules\Importer\Http\Сontrollers\ImporterController::class, 'getfile'])->name("send-file");

$router->get('/importer/result',	[\App\Modules\Importer\Http\Сontrollers\ImporterController::class, 'showLog'])->name("importer.show_log");

$router->get('/importer/{id}/csv',	[\App\Modules\Importer\Http\Сontrollers\ImporterController::class, 'exportCSV'])->name("importer.exportCSV");

$router->get('/importer/work_order',	[\App\Modules\Importer\Http\Сontrollers\ImporterController::class, 'showWorkOrder'])->name("importer.showWorkOrder");



