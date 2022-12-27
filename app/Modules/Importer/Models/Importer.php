<?php

namespace App\Modules\Importer\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Importer extends Model
{
	use HasFactory;

	protected $table = 'importer_log';

	public $result = 'empty';
	private $need_fields = array('ticket', 'urgency', 'rcvd date', 'category', 'store name');
	private $fields_for_import_url = array('ticket' => 'entityid');

	/**
	 * Search for all main tables in a file
	 *
	 * @param string $txt
	 * @param int $level
	 *
	 * @return array
	 */
	private function findSmallTable($txt, $level)
	{
		$result = array();
		while (preg_match_all("/<table([^>]*)>(.*?<\/table>)/msi", $txt, $match)) {
			$tmp = $this->findSmallTable($match[2][0], $level + 1);
			if (count($tmp) == 0) {
				$result[] = $match[0][0];
				if ($level == 0) {
					$txt = str_replace($result[count($result) - 1], "", $txt);
				} else {
					return $result;
				}
			} else {
				$result = array_merge($result, $tmp);
				if ($level == 0) {
					$txt = str_replace($result[count($result) - 1], "", $txt);
				} else {
					return $result;
				}
			}
		}
		return $result;
	}

	/**
	 * Search for a list of data to import
	 *
	 * @param string $tbl_content
	 *
	 * @return array
	 */
	private function parseSmallTable($tbl_content)
	{
		$result = array();
		$need_fields_indexs = array();
		$need_import = 1;
		if (preg_match_all("/<tr[^>]*?>(.*?)<\/tr>/msi", $tbl_content, $match_res)) {
			foreach ($match_res[1] as $k => $tr_item) {
				if (preg_match_all("/<th[^>]*?>(.*?)<\/th>/msi", $tr_item, $match_td)) {
					$need_import = 1;
					$tmp_th_list = array();
					foreach ($match_td[1] as $title_name) {
						$tmp_th_list[] = trim(strtolower(strip_tags($title_name)));
					}
					foreach ($this->need_fields as $item) {
						if (($search_ind = array_search($item, $tmp_th_list)) === false) {
							$need_import = 0;
							break;
						} else {
							$need_fields_indexs[$item] = $search_ind;
						}
					}
					if ($need_import == 1) {
						break;
					}
				} else {
					$need_import = 0;
				}
			}

			if ($need_import) {
				foreach ($match_res[1] as $td_list) {
					if (preg_match_all("/<td[^>]+>(.*?)<\/td>/msi", $td_list, $match_td)) {
						$tmp = array();
						foreach ($need_fields_indexs as $field_name => $position) {
							if (isset($match_td[1][$position]) && trim(strip_tags($match_td[1][$position])) != '') {
								$tmp[$field_name] = trim(strip_tags($match_td[1][$position]));
								if (isset($this->fields_for_import_url[$field_name])) {
									if (preg_match("/entityid=([\w]*)/msi", $match_td[1][$position], $match_url)) {
										$tmp['entityid'] = $match_url[1];
									}
								}
							}
						}
						if (count($tmp) >= count($need_fields_indexs)) {
							$result[] = $tmp;
						}
					}
				}
			}

		}
		return $result;

	}

	/**
	 * Saving data to the database and determining the result
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function prepareSaveData($data)
	{

		$work_order = new WorkOrderFromImporter();
		$result = array('saved' => array(), 'not_saved' => array());

		foreach ($data as $item) {
			if (!WorkOrderFromImporter::where('work_order_number', '=', $item['ticket'])->exists()) {
				$insert_arr = array(
					'work_order_number' => $item['ticket'],
					'external_id' => $item['entityid'],
					'priority' => $item['urgency'],
					'received_date' => Carbon::createFromFormat('n/j/Y', $item['rcvd date'])->format("Y-m-d"),
					'category' => $item['category'],
					'fin_loc' => $item['store name']
				);
				if ($work_order->insert($insert_arr)) {
					$result['saved'][] = $insert_arr;
				} else {
					$result['not_saved'][] = $insert_arr;
				}
			} else {
				$insert_arr = array(
					'work_order_number' => $item['ticket'],
					'external_id' => $item['entityid'],
					'priority' => $item['urgency'],
					'received_date' => Carbon::createFromFormat('n/j/Y', $item['rcvd date'])->format("Y-m-d"),
					'category' => $item['category'],
					'fin_loc' => $item['store name']
				);
				$result['not_saved'][] = $insert_arr;
			}
		}

		return $result;
	}

	/**
	 * Saving data to the database and determining the result
	 *
	 * @param string $content
	 * @param string $type_source
	 *
	 * @return int
	 */
	public function workWithFile(string $content, $type_source='html_form')
	{
		$result_for_log = array('saved' => array(), 'not_saved' => array());

		$small_table = $this->findSmallTable($content, 0);
		foreach ($small_table as $item) {
			$list_fields = $this->parseSmallTable($item);

			if (count($list_fields) == 0) {
			} else {
				$tmp_import_result = $this->prepareSaveData($list_fields);
				if (count($tmp_import_result['saved']) > 0) {
					$result_for_log['saved'] = array_merge($result_for_log['saved'], $tmp_import_result['saved']);
				}
				if (count($tmp_import_result['not_saved']) > 0) {
					$result_for_log['not_saved'] = array_merge($result_for_log['not_saved'], $tmp_import_result['not_saved']);
				}
			}
		}

		$this->type = $type_source;
		$this->run_at = Carbon::now();
		$this->entries_processed = json_encode($result_for_log['not_saved'], JSON_UNESCAPED_UNICODE);
		$this->entries_created = json_encode($result_for_log['saved'], JSON_UNESCAPED_UNICODE);
		$this->save();
		return $this->id;
	}







}
