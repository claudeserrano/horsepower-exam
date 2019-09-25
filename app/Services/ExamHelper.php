<?php

namespace App\Services;

class ExamHelper
{
	private $answers;
	private $examList;

	public function __construct()
	{
		$this->answers[0] = [1,2,4,1,2,4,3,2,4,4];
		$this->answers[1] = [2,3,4,1,2,1,3,1,4,3,2,3];
		$this->answers[2] = [2,4,2,2,3,2,4,1,1,1];
		$this->answers[3] = [3,4,3,3,1,1,1,3,2,3,4,2,4,2,1];
		$this->answers[4] = [2,2,1,3,3,[1,3],1,4,2,1,2,3,3,3,3,1,1,5,2,3,4];
		$this->answers[5] = [2,2,1,3,3,[1,3],1,4,2,1];
		$this->answers[6] = [2,3,3,3,3,1,2,5,2,3,4];

		$this->examList['hpe'] = [1,2,3,4];
		$this->examList['mfs'] = [6,7];
		//$this->examList['mfs_'] = [6,7];
	}

	public function getKey($level)
	{
		return $this->answers[$level - 1];
	}

	public function getExamList($job)
	{
		//return [1,2,3,4];
		return $this->examList[$job];
	}

}