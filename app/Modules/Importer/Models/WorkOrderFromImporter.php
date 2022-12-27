<?php

namespace App\Modules\Importer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderFromImporter extends Model
{
    use HasFactory;
	protected $table = 'work_order';
	protected $primaryKey = 'work_order_id';
	public $timestamps = false;


}
