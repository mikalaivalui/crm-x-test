<?php

namespace App\Modules\Importer\Http\Сontrollers;


use App\Http\Controllers\Controller;
use App\Modules\Importer\Http\Requests\ImporterRequest;
use App\Modules\Importer\Models\Importer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\File;


class ImporterController extends Controller
{
	/**
	 * Display form for upload file.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('Importer::index');
	}

	/**
	 * Showing work order list
	 *
	 *
	 * @return \Illuminate\Contracts\View\View
	 */
	public function showWorkOrder()
	{
		$work_order = DB::table('work_order')->orderBy('work_order_id', 'desc')->paginate(20);
		return view('Importer::work_order', ['work_order' => $work_order]);
	}


	/**
	 * Showing importer logs
	 *
	 *
	 * @return \Illuminate\Contracts\View\View
	 */

	public function showLog()
	{
		$importer_log = DB::table('importer_log')->orderBy('id', 'desc')->paginate(20);

		foreach ($importer_log->items() as &$item) {
			try {
				$item->entries_processed_arr = \Safe\json_decode($item->entries_processed, true);
				$item->entries_processed = count(\Safe\json_decode($item->entries_processed, true));

				$item->entries_created_arr = \Safe\json_decode($item->entries_created, true);
				$item->entries_created = count(\Safe\json_decode($item->entries_created, true));
			} catch (\Exception $e) {
				dd($e);
			}

		}

		return view('Importer::result', ['importer_log' => $importer_log]);
	}


	/**
	 * Showing importer logs
	 * @param string $path
	 *
	 *
	 * @return string
	 */
	public function getfileConsole($path)
	{
		if (file_exists($path)) {
			$importer = new Importer();
			$append_id = $importer->workWithFile(file_get_contents($path), 'cmd_line');
			$res = self::exportCSV($append_id, pathinfo($path)['dirname']);
			return "Data imported: " . realpath($res);
		} else {
			return 'File not exists: ' . $path;
		}
	}


	/**
	 * Processing the downloaded file
	 *
	 * @param \Illuminate\Foundation\Http\FormRequest $req
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getfile(ImporterRequest $req)
	{
		$importer = new Importer();
		$importer->workWithFile($req->file('file')->getContent());
		return redirect()->route('importer.show_log');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}

	/**
	 * Export CSV file to download
	 *
	 * @param int $id
	 * @param string $path
	 *
	 * @return mixed
	 * @throws mixed
	 */

	public function exportCSV($id, $path = '')
	{


		$fileName = 'importer.csv';

		$importer_log = DB::table('importer_log')
			->select('id', 'entries_processed', 'entries_created')
			->where('id', $id)
			->get();


		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=$fileName",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0"
		);

		$columns = array('status', 'work_order_number', 'external_id', 'priority', 'received_date', 'category', 'fin_loc');

		foreach ($importer_log as &$item) {
			try {
				$item->entries_processed_arr = \Safe\json_decode($item->entries_processed, true);
				$item->entries_created_arr = \Safe\json_decode($item->entries_created, true);
			} catch (\Exception $e) {
				dd($e);
			}
		}

		$callback = function () use ($importer_log, $columns, $path, $fileName) {

			function get($file, $item, $status)
			{
				$row['status'] = $status;
				$row['work_order_number'] = $item['work_order_number'];
				$row['external_id'] = $item['external_id'];
				$row['priority'] = $item['priority'];
				$row['received_date'] = $item['received_date'];
				$row['category'] = $item['category'];
				$row['fin_loc'] = $item['fin_loc'];
				fputcsv($file, array($row['status'], $row['work_order_number'], $row['external_id'], $row['priority'], $row['received_date'], $row['category'], $row['fin_loc']));
			}

			$output_path = 'php://output';
			if ($path != '') {
				$append_ind = 0;
				$tmp_fileName = $fileName;
				while (file_exists($path . '/' . $tmp_fileName)) {
					$append_ind++;
					$tmp_fileName = pathinfo($fileName)['filename'] . '(' . $append_ind . ').' . pathinfo($fileName)['extension'];
				}
				$output_path = $path . '/' . $tmp_fileName;
			}

			$file = fopen($output_path, 'w');
			fputcsv($file, $columns);

			foreach ($importer_log as $item_row) {
				foreach ($item_row->entries_created_arr as $item) {
					get($file, $item, 'saved');
				}
				foreach ($item_row->entries_processed_arr as $item) {
					get($file, $item, 'not saved');
				}
			}

			fclose($file);
			if ($path != '') {
				return $output_path;
			}
		};

		if ($path == '') {
			return response()->stream($callback, 200, $headers);
		} else {
			return call_user_func($callback);
		}
	}


}
