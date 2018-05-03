<?php

require_once './../../PHPExcel/PHPExcel.php';

class MonthlyReport 
{

	function __construct()
	{
	}

	public function getReportData($issues, $timeEntries)
	{
		$result = array();
		foreach ($timeEntries as $timeEntry) {
			foreach ($issues as $issue) {
				if ($timeEntry['issue_id'] == $issue['issue_id'] && $timeEntry['project_name'] == $issue['project_name'] && $timeEntry['spent_time'] != 0) {
					$temp = array_merge($issue,$timeEntry);
					array_push($result, $temp);
				}
			}
		}
		return $result;	
	}

	public function getReportDataByUser($data, &$reportData)
	{
		foreach ($data as $data) {
			$spentDay = date('j', strtotime($data['spent_on']));
			if ($reportData[$data['user_name']][$spentDay] != null) {
				$reportData[$data['user_name']][$spentDay] .= "\r\n";
			}
			$reportData[$data['user_name']][$spentDay] .= $data['subject'];
		}
	}

	public function exportReportFile($startDate, $dueDate, $data)
	{
		// Create new PHPExcel object
		$objPHPExcel 	= new PHPExcel();
		$startDate 		= date("Ymd", strtotime($startDate));
		$dueDate 		= date("Ymd", strtotime($dueDate));
		$startDay 		= date('j', strtotime($startDate));
		$dueDay 		= date('j', strtotime($dueDate));
		$fileName 	 	= 'Co-well 作業報告書_'. date("Ym", strtotime($startDate)) .'.xlsx';

		// Unset ticket not used
    	$userArr 	  = array('Pham Thinh', 'QA HuongLH6380', 'Dev Chinhlv6812', 'Dev HienTQ-6724');
    	$issue_id = array();
    	$project_name  = array();
    	foreach ($data as $key => $value) {
    		if ($value['spent_time'] == 0 || !in_array ($value['user_name'], $userArr)) {
    			unset($data[$key]);
    		} else {
			    $issue_id[$key]  = $value['issue_id'];
			    $project_name[$key] 	= $value['project_name'];
			}
		}
		array_multisort($issue_id, SORT_ASC, $project_name, SORT_ASC, $data);

		// Create report data
		$reportData = array();
		foreach ($userArr as $user) {
			for ($i = $startDay; $i <= $dueDay; $i++) {
				$reportData[$user][$i] = '';
			}
		}

		$this->getReportDataByUser($data, $reportData);
		$stt = 0;
		foreach (array_keys($reportData) as $user) {
			$objPHPSheet = $objPHPExcel->createSheet($stt);
			$objPHPSheet->setTitle($user);
			for ($i = $startDay; $i <= $dueDay; $i ++) {
				$objPHPSheet->setCellValue('B' . $i, $reportData[$user][$i]);
				$objPHPSheet->getStyle('B' . $i)->getAlignment()->setWrapText(true);
				$objPHPSheet->mergeCells('B'. $i .':C' . $i);
			}
			$objPHPSheet->getColumnDimension('B')->setWidth(50);
			$style = array('font' => array('size' => 8, 'name'  => 'Arial'));
			$objPHPSheet->getStyle('B'. $startDay .':B' . $dueDay)->applyFromArray($style);
			$objPHPSheet->getStyle('B'. $startDay .':C' . $dueDay)->applyFromArray(
			    array(
			        'borders' => array(
			            'allborders' => array(
			                'style' => PHPExcel_Style_Border::BORDER_THIN,
			                'color' => array('rgb' => '000000')
			            )
			        )
			    )
			);
			$stt ++;
		}

	    header('content-type:application/csv;charset=UTF-8');
		header('Content-Disposition: attachment;filename="' . $fileName . '"');
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
