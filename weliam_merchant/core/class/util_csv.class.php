<?php
//dezend by http://www.sucaihuo.com/
class util_csv
{
	static public function read_csv_lines($csv_file = '', $lines = 0, $offset = 0)
	{
		if (!($fp = fopen($csv_file, 'r'))) {
			return false;
		}

		$i = $j = 0;

		if (false !== ($line = fgets($fp))) {
			if ($i++ < $offset) {
				continue;
			}

			break;
		}

		$data = array();

		while ($j++ < $lines && !feof($fp)) {
			$data[] = fgetcsv($fp);
		}

		fclose($fp);
		return $data;
	}

	static public function export_csv_1($data = array(), $header_data = array(), $file_name = '')
	{
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $file_name);

		if (!empty($header_data)) {
			echo iconv('utf-8', 'gbk//TRANSLIT', '"' . implode('","', $header_data) . '"' . '
');
		}

		foreach ($data as $key => $value) {
			$output = array();
			$output[] = $value['id'];
			$output[] = $value['name'];
			echo iconv('utf-8', 'gbk//TRANSLIT', '"' . implode('","', $output) . '"
');
		}
	}

	static public function export_csv_2($data = array(), $header_data = array(), $file_name = '')
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename=' . $file_name);
		header('Cache-Control: max-age=0');
		$fp = fopen('php://output', 'a');

		if (!empty($header_data)) {
			foreach ($header_data as $key => $value) {
				$header_data[$key] = iconv('utf-8', 'gbk', $value);
			}

			fputcsv($fp, $header_data);
		}

		$num = 0;
		$limit = 100000;
		$count = count($data);

		if (0 < $count) {
			$i = 0;

			while ($i < $count) {
				++$num;

				if ($limit == $num) {
					ob_flush();
					flush();
					$num = 0;
				}

				$row = $data[$i];

				foreach ($row as $key => $value) {
					$row[$key] = iconv('utf-8', 'gbk', $value);
				}

				fputcsv($fp, $row);
				++$i;
			}
		}

		fclose($fp);
	}
}

?>
