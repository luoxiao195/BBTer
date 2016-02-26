<?php
function test(){
	echo 'hehe';
}
function getTransferClass($transferInteger){
	for($i = 0 ; $i < 6 ; $i ++){
		for($j = 0 ; $j < 7 ; $j ++){
			if($transferInteger & 1 == 1){
				$transferClass[$i][$j] = 1;
					
			
			}else{
				$transferClass[$i][$j] = 0;
		
			}

			$transferInteger = (double)($transferInteger / 2);
			if($transferInteger < 0)
				break;
		}
		if($transferInteger < 0)
			break;
	}
	return $transferClass;
}

function transferLessionToText($lession){
	switch ($lession) {
		case 1:
			# code...
			return '一二节';
			break;
		case 2:
			return '三四节';
			# code...
			break;
		case 3:
			return '五六节';
			# code...
			break;
		case 4:
			return '第七节';
			# code...
			break;
		case 5: 
			return '第八节';
		case  6:
			return '晚上';
		default:
			# code...
			break;
	}
}
function transferDayToText($day){
	switch ($day) {
		case 1:
			# code...
			return '周一';
			break;
		case 2:
			return '周二';
			# code...
			break;
		case 3:
			return '周三';
			# code...
			break;
		case 4:
			return '周四';
			# code...
			break;
		case 5: 
			return '周五';
		case  6:
			return '周六';
		case 7:
			return '周日';
		default:
			# code...
			break;
	}
}

function transferDayToInt($day){
	switch ($day) {
		case '周一':
			# code...
			return 1;
			break;
		case '周二':
			return 2;			# code...
			break;
		case '周三':
			return 3;			# code...
			break;
		case '周四':
			return 4;			# code...
			break;
		case '周五': 
			return 5;
			break;		
		case  '周六':
			return 6;
			break;		
		case '周日':
			return 7;
			break;		
		default:
			# code...
			break;
	}
}