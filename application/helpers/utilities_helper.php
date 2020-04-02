<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function sanitize($in) {
	return addslashes(htmlspecialchars(strip_tags(trim($in))));
}

function removeSpecialchar($value){
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $value); // Removes special chars.
	return $string;
}

function generate_json($data) {
	header("access-control-allow-origin: *");
	header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
	header('Content-type: application/json');
	echo json_encode($data);
}

function today() {
	date_default_timezone_set('Asia/Manila');
	return date("Y-m-d");
}

function today_text() {
	date_default_timezone_set('Asia/Manila');
	return date("m/d/Y");
}

function today_date() {
	date_default_timezone_set('Asia/Manila');
	return date("m/d/Y");
}

function time_only() {
	date_default_timezone_set('Asia/Manila');
	return date("G:i");
}

function time_only_sec() {
	date_default_timezone_set('Asia/Manila');
	return date("G:i:s");
}

function year_only() {
	date_default_timezone_set('Asia/Manila');
	return date("Y");
}

function month_year(){
	date_default_timezone_set('Asia/Manila');
	return date("M_Y");
}

function todaytime() {
	date_default_timezone_set('Asia/Manila');
	return date("Y-m-d G:i:s");
}

function fulldate() {
	date_default_timezone_set('Asia/Manila');
	return date("F d, Y");
}

function convert_time($time){
	if($time != ""){
		$time = explode(":",$time);
		return $time[0].":".$time[1];
	}else{
		return "";
	}

}

function en_dec($action, $string){ //used for token
	$output = false;

	$encrypt_method = "AES-256-CBC";
	$secret_key = 'CloudPandaPHInc';
	$secret_iv = 'TheDarkHorseRule';

	// hash
	$key = hash('sha256', $secret_key);

	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', $secret_iv), 0, 16);

	if( $action == 'en' )
	{
	  $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	  $output = base64_encode($output);
	}
	else if( $action == 'dec' )
	{
	  $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}

	return $output;
}

function Generate_random_password() {
    $alphabet = "abcdefghijklmnopqrstuwxyz";
    $alphabetUpper = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
    $alphabetNumber = "0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabetNumber) - 1; //put the length -1 in cache
    for ($i = 0; $i < 3; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n].$alphabetUpper[$n].$alphabetNumber[$n];
    }
    return implode($pass); //turn the array into a string
}

function generate_player_no(){
	$letters = array("A","B","C","D","E",
					 "F","G","H","I","J",
					 "K","L","M","N","O",
					 "P","Q","R","S","T",
					 "U","V","W","X","Y",
					 "Z");

	$numbers = array("1","2","3","4","5",
					 "6","7","8","9","0");

	$generated_key = array();
	for($x=0; $x < 11; $x++){
		if (count($generated_key) < 4) {
			$get_val = array_rand($letters, 1);

			array_push($generated_key, $letters[$get_val]);
		}else{
			$get_val = array_rand($numbers, 1);
			array_push($generated_key, $numbers[$get_val]);
		}
	}
	$generated_key = implode("",$generated_key);

	return $generated_key;
}

function remove_format($text){
	$text = str_replace(",", "", $text);
	$unformatted = explode('.', $text);
    return $unformatted[0];
}

function remove_format2($text){
	$text = str_replace(",", "", $text);
	$unformatted = explode('.', $text);
    return $unformatted[0];
}

//ricky
function formatDate($date) {
  $date1 = preg_split ("/\-/", $date);
  $month = [
      '01'=>'Jan',
      '02'=>'Feb',
      '03'=>'Mar',
      '04'=>'Apr',
      '05'=>'May',
      '06'=>'Jun',
      '07'=>'Jul',
      '08'=>'Aug',
      '09'=>'Sep',
      '10'=>'Oct',
      '11'=>'Nov',
      '12'=>'Dec'];
  return $month[$date1[1]]." ".$date1[2].", ".$date1[0];
}

### Marky ###

function switch_database($db_name){
	$conn =& get_instance();
	$config['dsn'] = 'mysql:host=localhost;dbname='.$db_name;
	$config['username'] = 'root';
	$config['password'] = '';
	$config['dbdriver'] = 'pdo';
	$config['dbprefix'] = '';
	$config['pconnect'] = FALSE;
	$config['db_debug'] = TRUE;
	$config['cache_on'] = FALSE;
	$config['cachedir'] = '';
	$config['char_set'] = 'utf8';
	$config['dbcollat'] = 'utf8_general_ci';
	return $conn->load->database($config, TRUE);
}

function secret_key($timezone){
	date_default_timezone_set($timezone);
	$arr = json_encode(array('CloudPandaPHInc',date("Y-m-d")));
	$key = en_dec('en',$arr);
	return $key;
}

function filter_array($arr,$condition){
	$matches = array();
	foreach($arr as $row){
		if($row['employee_idno'] == $condition['id'] && $row['timelog_date'] == $condition['date']){
			$matches[] = $row;
		}
	}

	return $matches;
}

function filter_array2($arr,$condition){
	$matches = array();

	if(count((array)$arr) > 0){
		foreach($arr as $row){
			unset($row['id']);
			if($row['employee_idno'] == $condition){
				$matches[] = $row;
			}
		}
	}

	return $matches;
}

function filter_array3($arr,$condition){
	$matches = array();

	if(count((array)$arr) > 0){
		foreach($arr as $row){
			unset($row['id']);
			if($row['applicant_ref_no'] == $condition){
				$matches[] = $row;
			}
		}
	}

	return $matches;
}

function filter_array_payroll($arr,$condition){
	$matches = array();
	foreach($arr as $row){
		if($row->date == $condition['date']){
			$matches[] = $row;
		}
	}

	return $matches;
}

function filter_requirements($arr,$condition){
	$matches = array();
	foreach($arr as $row){
		if($row['req_type'] == $condition){
			$matches[] = $row;
		}
	}
	return $matches;
}

function filter_workschedule($arr,$condition){
	$data = "";
	foreach($arr as $row){
		if($row['type'] == 'department'){
			if($row['department_id'] == $condition['dept'] && ($condition['date'] >= $row['date_from'] && $condition['date'] <= $row['date_to'])){
				$data = $row;
			}
		}else{
			if($row['department_id'] ==  $condition['dept'] && $row['employee_idno'] == $condition['id'] && ($condition['date'] >= $row['date_from'] && $condition['date'] <= $row['date_to'])){
				$data = $row;
			}
		}
	}
	return $data;
}

function filter_offset($arr,$cond){
	$matches = array('late' => 0, 'undertime' => 0, 'wholeday' => 0, 'halfday' => 0);
	foreach($arr as $row){
		if($row['employee_idno'] == $cond['id'] && $row['date_rendered'] == $cond['date']){
			$matches['late'] = ($row['offset_type'] == 'late') ? $row['offset_min'] : 0;
			$matches['undertime'] = ($row['offset_type'] == 'undertime') ? $row['offset_min'] : 0;
			$matches['wholeday'] = ($row['offset_type'] == 'wholeday') ? $row['offset_min'] : 0;
			$matches['halfday'] = ($row['offset_type'] == 'halfday') ? $row['offset_min'] : 0;
		}
	}
	return $matches;
}

function compute_timelog($data,$return,$grace = false,$nightdiff_status = 'off',$nightdiff_start = 1320,$nightdiff_end = 1800){
	$total_minutes = 0;
	$real_total_min = 0;
	$overtime = 0;
	$undertime = 0;
	$overbreak = 0;
	$manhours = 0;
	$late = 0;
	$night_diff = 0;
	$test = 0;

	$employee_idno = $data['employee_idno'];
	$total_whours = $data['total_whours'];
	$total_bhours = $data['total_bhours'];
	$total_whours_less_break = $total_whours - $total_bhours;
	$sched_type = $data['sched_type'];
	$stime_in = $data['stime_in'];
	$stime_out = $data['stime_out'];
	$sbreak_in = $data['sbreak_in'];
	$sbreak_out = $data['sbreak_out'];
	$first_in = $data['first_in'];
	$last_out = $data['last_out'];

	$min_first_in = mins($first_in);
	$min_stime_in = mins($stime_in);
	$min_last_out = mins($last_out);
	$min_stime_out = mins($stime_out);
	$min_break_in = mins($sbreak_in);
	$rbreak_in = $min_break_in;
	$min_break_out = mins($sbreak_out);
	$rbreak_out = $min_break_out;
	$min_total_whours = $total_whours_less_break * 60;
	$min_total_bhours = $total_bhours * 60;

	if(count($data['timelog']) > 0){
		###  FIX SCHEDULE ###
		if($sched_type == "fix"){
			$time_arr = array();

			### TOTAL MINUTES ###
			$time = $data['timelog'];
			$first_time_in = $min_first_in;
			$first_time_out = mins($time[0]['time_out']);
			$last_time_in = mins($time[count($time) - 1]['time_in']);
			$last_time_out = $min_last_out;
			for($a = 0; $a < count($time); $a++){
				if($a == 0){
					$tmp_in = ($min_stime_in > mins($time[$a]['time_in']) && ($min_stime_in - mins($time[$a]['time_in'])) > 720) ? mins($time[$a]['time_in']) + 1440 : mins($time[$a]['time_in']);
					$in = ($min_stime_in > $tmp_in) ? $min_stime_in : mins($time[$a]['time_in']);
					$in = (mins($time[$a]['time_in']) >= $min_break_in && mins($time[$a]['time_in']) <= $min_break_out) ? $min_break_out : $in;
				}else{
					$in = (mins($time[$a]['time_in']) >= $min_break_in && mins($time[$a]['time_in']) <= $min_break_out) ? $min_break_out : mins($time[$a]['time_in']);
					if($min_stime_in > $min_stime_out &&  ((mins($time[$a]['time_in']) - $min_stime_in) < 0)){
						$in = (mins($time[$a]['time_in']) >= $min_break_in && mins($time[$a]['time_in']) <= $min_break_out) ? $min_break_out : $in;
					}
				}

				if($a == count($time) - 1){
					$tmp_out = (mins($time[$a]['time_out']) > $min_stime_out && (mins($time[$a]['time_out']) - $min_stime_out) > 720) ? $min_stime_out + 1440 : $min_stime_out;
					$out = (mins($time[$a]['time_out']) > $tmp_out) ? $min_stime_out : mins($time[$a]['time_out']);
					$out = (mins($time[$a]['time_out']) >= $min_break_in && mins($time[$a]['time_out']) <= $min_break_out) ? $min_break_in : $out;
				}else{
					$out = (mins($time[$a]['time_out']) >= $min_break_in && mins($time[$a]['time_out']) <= $min_break_out) ? $min_break_in : mins($time[$a]['time_out']);
					if(mins($time[$a]['time_in']) > mins($time[$a]['time_out']) && (mins($time[$a]['time_out']) - $min_stime_out) < 0){
						$out = (mins($time[$a]['time_out']) >= $min_break_in && mins($time[$a]['time_out']) <= $min_break_out) ? $min_break_in : $out;
					}
				}

				$time_arr[] = $in;
				$time_arr[] = $out;
			}

			### COMPUTE LATE ###
			if($min_first_in > $min_stime_in && ($min_first_in <= $min_stime_out || ($min_stime_out - $min_first_in) < 0)){
				$min_first_in = ($min_first_in <= $min_break_out && $min_first_in >= $min_break_in) ? $min_break_out : $min_first_in;
				$late = $min_first_in - $min_stime_in;
				if($min_break_out <= $first_time_in && $min_stime_in < $min_stime_out){
					$late -= $min_total_bhours;
				}
			}

			### COMPUTE LATE NIGHT SHIFT ###
			$tmp_first_in = (($min_stime_in > $min_first_in) && ($min_stime_in - $min_first_in) > 720) ? $min_first_in + 1440 : $min_first_in;
			if($tmp_first_in > $min_stime_in && ($min_first_in - $min_stime_in) < 0){
				$rmin_first_in = $min_first_in;
				$min_first_in = (($min_first_in - $min_stime_in) < 0) ? $min_first_in + 1440 : $min_first_in ;
				if($min_first_in > $min_stime_in){
					// $min_break_out = $min_break_out + 1440;
					$min_first_in = ($rmin_first_in <= $min_break_out && $rmin_first_in >= $min_break_in) ? $min_break_out + 1440 : $min_first_in;
					$late = abs($min_first_in - $min_stime_in);
					if($min_break_out <= $rmin_first_in){
						$late -= $min_total_bhours;
					}
				}
			}

			### COMPUTE OVERBREAK ###
			$break_start = get_nearest($min_break_in,$time_arr,'out'); // start of break = time_out
			$break_end = get_nearest($min_break_out,$time_arr,'in'); // end of break = time_in
			if($break_end['out'] > 0 && count((array)$time_arr) > 2){
				$test = 1;
				if($break_end['closest'] > $break_start['closest']){
					$test = 2;
					$total_sbhours = $min_break_out - $min_break_in;
					$total_actual_break = $break_end['closest'] - $break_start['closest'];
					if($total_actual_break > $total_sbhours){
						$break1 = ($min_break_in > $break_start['closest']) ? ($min_break_in - $break_start['closest']) : 0;
						$break2 = ($break_end['closest'] > $min_break_out) ? ($break_end['closest'] - $min_break_out) : 0;
						$overbreak = ($break1 + $break2);
						// $test = $overbreak;
					}
				}
			}

			### COMPUTE TOTAL MINUTES ###
			for($b = 0; $b < count($time); $b++){
				$checktmp_out = (mins($time[$b]['time_out']) > $min_stime_out && (mins($time[$b]['time_out']) - $min_stime_out) > 720) ? $min_stime_out + 1440 : $min_stime_out;
				if((mins($time[$b]['time_in']) < $checktmp_out && mins($time[$b]['time_out']) > $min_stime_in)){
					if($b == 0){
						$tmp_in = ($min_stime_in > mins($time[$b]['time_in']) && ($min_stime_in - mins($time[$b]['time_in'])) > 720) ? mins($time[$b]['time_in']) + 1440 : mins($time[$b]['time_in']);
						$in = ($min_stime_in > $tmp_in) ? $min_stime_in : mins($time[$b]['time_in']);
						$in = (mins($time[$b]['time_in']) >= $min_break_in && mins($time[$b]['time_in']) <= $min_break_out) ? $min_break_out : mins($time[$b]['time_in']);
						$real_in = (mins($time[$b]['time_in']) >= $min_break_in && mins($time[$b]['time_in']) <= $min_break_out) ? $min_break_out : $in;
					}else{
						$in = (mins($time[$b]['time_in']) >= $min_break_in && mins($time[$b]['time_in']) <= $min_break_out) ? $min_break_out : mins($time[$b]['time_in']);
						$real_in = (mins($time[$b]['time_in']) >= $min_break_in && mins($time[$b]['time_in']) <= $min_break_out) ? $min_break_out : mins($time[$b]['time_in']);
						if($min_stime_in > $min_stime_out &&  ((mins($time[$b]['time_in']) - $min_stime_in) < 0)){
							$in = (mins($time[$b]['time_in']) >= $min_break_in && mins($time[$b]['time_in']) <= $min_break_out) ? $min_break_out : $in;
							$real_in = (mins($time[$b]['time_in']) >= $min_break_in && mins($time[$b]['time_in']) <= $min_break_out) ? $min_break_out : $in;
						}
					}

					if($b == count($time) - 1){
						$tmp_out = (mins($time[$b]['time_out']) > $min_stime_out && (mins($time[$b]['time_out']) - $min_stime_out) > 720) ? $min_stime_out + 1440 : $min_stime_out;
						$out = (mins($time[$b]['time_out']) > $tmp_out) ? $min_stime_out : mins($time[$b]['time_out']);
						$out = (mins($time[$b]['time_out']) >= $min_break_in && mins($time[$b]['time_out']) <= $min_break_out) ? $min_break_in : $out;
						$real_out = (mins($time[$b]['time_out']) >= $min_break_in && mins($time[$b]['time_out']) <= $min_break_out) ? $min_break_in : mins($time[$b]['time_out']);
					}else{
						$out = (mins($time[$b]['time_out']) >= $min_break_in && mins($time[$b]['time_out']) <= $min_break_out) ? $min_break_in : mins($time[$b]['time_out']);
						$real_out = (mins($time[$b]['time_out']) >= $min_break_in && mins($time[$b]['time_out']) <= $min_break_out) ? $min_break_in : mins($time[$b]['time_out']);
						if(mins($time[$b]['time_in']) > mins($time[$b]['time_out']) && (mins($time[$b]['time_out']) - $min_stime_out) < 0){
							$out = (mins($time[$b]['time_out']) >= $min_break_in && mins($time[$b]['time_out']) <= $min_break_out) ? $min_break_in : $out;
							$real_out = (mins($time[$b]['time_out']) >= $min_break_in && mins($time[$b]['time_out']) <= $min_break_out) ? $min_break_in : mins($time[$b]['time_out']);
						}
					}

					$total_minutes += ($in > $out) ? ($out + 1440) - $in : ($out - $in);
					$real_total_min += ($real_in > $real_out) ? ($real_out + 1440) - $real_in : ($real_out - $real_in);

					if(mins($time[$b]['time_in']) > mins($time[$b]['time_out'])){
						$min_break_in = (mins($time[$b]['time_in']) > $min_break_in) ? $min_break_in + 1440 : $min_break_in;
					}

					if(mins($time[$b]['time_in']) < $min_break_in && mins($time[$b]['time_out']) > $min_break_out){
						$total_minutes -= $min_total_bhours;
						$real_total_min -= $min_total_bhours;
					}

					### NIGHT DIFF ###
					if(($in >= $nightdiff_start || $in <= $nightdiff_end) && ($out >= $nightdiff_start || $out <= $nightdiff_end)){
						$night_in = $in;
						$night_out = ($in > $out) ? $out + 1440 : $out;
						$nightdiff_start = ($in > 0 && $in < 360) ? 0 : $nightdiff_start;
						if($night_in >= $nightdiff_start && $night_out <= $nightdiff_end){
							$night_diff += $night_out - $night_in;
						}
						// LEFT OVERLAP
						if($night_in < $nightdiff_start && $night_out > $nightdiff_start && $night_out <= $nightdiff_end){
							$night_diff += $night_out - $nightdiff_start;
						}
						// RIGHT OVERLAP
						if($night_in >= $nightdiff_start && $night_in < $nightdiff_end && $night_out > $nightdiff_end){
							$night_diff += $nightdiff_end - $night_in;
						}

						if($min_first_in > $min_last_out && $min_first_in > $rbreak_in){
							$night_break_in = $min_break_in + 1440;
							$night_break_out = $min_break_out + 1440;
							$night_last_out = $min_last_out + 1440;
							$night_first_in = $min_first_in;
							$night_diff -= ($night_first_in <= $night_break_in && $night_last_out >= $night_break_out) ? $min_total_bhours : 0;
						}

					}
				}
			}

			### MANHOURS ###
			$manhours = ($total_minutes) / 60;

			$late = ($late < 0) ? $late + $min_total_bhours : $late;
			$overbreak = ($overbreak < 0) ? $overbreak + $min_total_bhours : $overbreak;

			### COMPUTE UNDERTIME ###
			$stotal_minutes = $min_total_whours;
			if($stotal_minutes > $total_minutes){
				$undertime = $stotal_minutes - ($total_minutes + $late + $overbreak);
			}

			$undertime = ($undertime < 0) ? $undertime + $min_total_bhours : $undertime;

		}

		### FLEXI SCHEDULE ###
		if($sched_type == "flexi"){
			### TOTAL MINUTES ###
			if(count($data['timelog']) > 1){
				foreach($data['timelog'] as $time){
					$in = mins($time['time_in']);
					$out = mins($time['time_out']);
					$total_minutes += ($in > $out) ? ($out + 1440) - $in : ($out - $in);

					### NIGHT DIFF ###
					if(($in >= $nightdiff_start || $in <= $nightdiff_end) && ($out >= $nightdiff_start || $out <= $nightdiff_end)){
						$night_in = $in;
						$night_out = ($in > $out) ? $out + 1440 : $out;
						$nightdiff_start = ($in > 0 && $in < 360) ? 0 : $nightdiff_start;
						// FULL OVERLAP
						if($night_in >= $nightdiff_start && $night_out <= $nightdiff_end){
							$night_diff += $night_out - $night_in;
						}
						// COMPLETE FULL OVERLAP
						if($night_in <= $nightdiff_start && $night_out >= $nightdiff_end){
							$night_diff += $nightdiff_start - $nightdiff_end;
						}
						// LEFT OVERLAP
						if($night_in < $nightdiff_start && $night_out > $nightdiff_start && $night_out <= $nightdiff_end){
							$night_diff += $night_out - $nightdiff_start;
						}
						// RIGHT OVERLAP
						if($night_in >= $nightdiff_start && $night_in < $nightdiff_end && $night_out > $nightdiff_end){
							$night_diff += $nightdiff_end - $night_in;
						}
					}
				}
			}else{
				$in = mins($data['timelog'][0]['time_in']);
				$out = mins($data['timelog'][0]['time_out']);
				$total_minutes += ($in > $out) ? ($out + 1440) - $in : ($out - $in);


				### NIGHT DIFF ###
				if(($in >= $nightdiff_start || $in <= $nightdiff_end) && ($out >= $nightdiff_start || $out <= $nightdiff_end)){
					$night_in = $in;
					$night_out = ($in > $out) ? $out + 1440 : $out;
					$nightdiff_start = ($in > 0 && $in < 360) ? 0 : $nightdiff_start;
					// FULL OVERLAP
					if($night_in >= $nightdiff_start && $night_out <= $nightdiff_end){
						$night_diff += $night_out - $night_in;
					}
					// COMPLETE FULL OVERLAP
					if($night_in <= $nightdiff_start && $night_out >= $nightdiff_end){
						$night_diff += $nightdiff_end - $nightdiff_start;
					}
					// LEFT OVERLAP
					if($night_in < $nightdiff_start && $night_out > $nightdiff_start && $night_out <= $nightdiff_end){
						$night_diff += $night_out - $nightdiff_start;
					}
					// RIGHT OVERLAP
					if($night_in >= $nightdiff_start && $night_in < $nightdiff_end && $night_out > $nightdiff_end){
						$night_diff += $nightdiff_end - $night_in;
					}
				}

				if($total_minutes > ($min_total_whours / 2)){
					$total_minutes -= $min_total_bhours;
					$night_diff -= $min_total_bhours;
				}
			}

			$real_total_min = $total_minutes;

			### MAN HOURS ###
			$manhours = $total_minutes / 60;

			### UNDERTIME ###
			$stotal_minutes = $min_total_whours;
			if($stotal_minutes > $total_minutes){
				$undertime = ($stotal_minutes - $total_minutes);
			}
		}
	}
	$night_diff = round($night_diff / 60, 2);

	$real_late = $late;
	$real_undertime = $undertime;

	$late = (($late - $grace['late']) < 0) ? 0 : ($late - $grace['late']);
	$late = (($late - $grace['offset_late']) < 0) ? 0 : ($late - $grace['offset_late']);
	$glate = ($late == 0) ? $real_late : $grace['late'];
	$offset_late = ($late == 0) ? $real_late : $grace['offset_late'];

	$undertime = (($undertime - $grace['undertime']) < 0) ? 0 : ($undertime - $grace['undertime']);
	$undertime = (($undertime - $grace['offset_undertime']) < 0) ? 0 : ($undertime - $grace['offset_undertime']);
	$gundertime = ($undertime == 0) ? $real_undertime : $grace['undertime'];
	$offset_undertime = ($undertime == 0) ? $real_undertime : $grace['offset_undertime'];

	$total_minutes = ($total_minutes > $min_total_whours) ? $min_total_whours : $total_minutes;
	$total_minutes += ($grace['late'] > 0) ? $glate : 0;
	$total_minutes += ($grace['offset_late'] > 0) ? $offset_late : 0;
	$total_minutes += ($grace['undertime'] > 0) ? $gundertime : 0;
	$total_minutes += ($grace['offset_undertime'] > 0) ? $offset_undertime : 0;

	$total_minutes -= ($total_minutes == $min_total_whours && $late > 0) ? $late : 0;
	$total_minutes -= ($total_minutes == $min_total_whours && $undertime > 0) ? $undertime : 0;
	$total_minutes -= ($total_minutes == $min_total_whours && $overbreak > 0) ? $overbreak : 0;

	$manhours = $total_minutes / 60;
	$night_diff = ($night_diff < 0) ? 0 : $night_diff;
	$night_diff = ($nightdiff_status == 'off') ? 0 : $night_diff;
	$total_minutes = ($total_minutes < 0) ? 0 : $total_minutes;
	$total_minutes = ($total_minutes > $min_total_whours) ? $min_total_whours : $total_minutes;
	### RETURN ###
	switch ($return) {
		case 'manhours':
			$manhours = (round($manhours,2) > $total_whours_less_break) ? $total_whours_less_break : round($manhours,2);
			return $manhours;
			break;
		case 'late':
			return $late;
			break;
		case 'undertime':
			return $undertime;
			break;
		case 'total_minutes':
			return $total_minutes;
			break;
		case 'overtime':
			$real_total_min += ($late > 0) ? $late : 0;
			$real_total_min += ($overbreak > 0) ? $overbreak : 0;
			$overtime = (($real_total_min) > ($min_total_whours))
			? $real_total_min - ($min_total_whours)
			: 0;
			return $overtime;
			break;
		case 'night_diff':
			return $night_diff;
			break;
		case 'overbreak':
			return $overbreak;
			break;
		case 'all':
			$manhours = (round($manhours,2) > $total_whours_less_break) ? $total_whours_less_break : round($manhours,2);
			$return_data = array(
				"total_minutes" => $total_minutes,
				"manhours" => $manhours,
				"late" => $late,
				"undertime" => $undertime,
				"overbreak" => $overbreak,
				"night_diff" => $night_diff
			);
			return $return_data;
		default:
			// code...
			break;
	}
}

function get_nearest($search,$arr,$index){
	$closest = null;
	$in = "";

	// MULTIPLE TIMELOG
	if(count($arr) > 2){
		for($x = 0; $x < count($arr); $x++){
			if ($closest === null || abs($search - $closest) > abs($arr[$x] - $search)) {
				$closest = $arr[$x];
				$in = $x;
			}
		}

		if($index == 'in'){
			if($in % 2 == 0){
				$nearest = array("closest" => $closest, "out" => $arr[$in + 1]);
				return $nearest;
				// return $closest;
			}else{
				if((count($arr) - 1) == $in){
					$in = $in - 1;
				}else{
					$in = $in + 1;
				}
				$nearest = array("closest" => $arr[$in - 1], "out" => $arr[$in]);
				return $nearest;
				// return $arr[$in - 1]; // IMPOSSIBLE TO HAPPEN IN THEORY HAHA
			}
		}

		if($index == 'out'){
			if($in % 2 != 0){
				if((count($arr) - 1) == $in){
					$in = $in - 1;
				}else{
					$in = $in + 1;
				}
				$nearest = array("closest" => $closest, "out" => $arr[$in]);
				return $nearest;
				// return $closest;
			}else{
				if($in == 0){
					$in = $in;
				}else{
					$in = $in - 1;
				}
				$nearest = array("closest" => $arr[$in], "out" => $arr[$in + 1]);
				return $nearest;
				// return $arr[$in - 1];
			}
		}

	}

	// SINGLE TIMELOG
	if(count($arr) <= 2){
		if($index == 'in'){
			$nearest = array("closest" => $arr[0], "out" => $arr[1]);
			return $nearest;
		}

		if($index == 'out'){
			$nearest = array("closest" => $arr[1], "out" => $arr[0]);
			return $nearest;
		}
	}
}

function time_diff($start,$end){
	$start = strtotime($start);
	$end = strtotime($end);
	$diff = $end - $start;
	return round(abs($diff) / 60);
}

function days_between($date1, $date2){
	$date1 = date_create($date1);
	$date2 = date_create($date2);
	$diff = date_diff($date1,$date2);
	// return (int)$diff->format("%a") + 1;
	return (int)$diff->format("%a");
}

function compute_philhealth($salary,$p_frequency){
	$ph = ((float)$salary * 0.0275) / 2; // divide by 2 for employee and employer share
	$ph = $ph / (int)$p_frequency;
	return  $ph;
}

function compute_pagibig($share,$salary,$p_frequency){
	$max = 5000;
	$min = 1500;
	if((float)$salary > $min){
		$love = $max * ($share / 100);
	}else{
		$love = (float)$salary * ($share / 100);
	}

	return $love / (int)$p_frequency;
}

function per_hr_rate($total_sal){
	$hr_rate = (((float)$total_sal * 12) / 365) / 8 ;
	return $hr_rate;
}

function per_min_rate($total_sal){
	$hr_rate = ((((float)$total_sal * 12) / 365) / 8);
	$minute_rate = $hr_rate / 60;
	return $minute_rate;
}

function hr_rate($total_sal,$total_hours,$total_break){
	$hr_rate = (($total_sal * 12) / 365) / ($total_hours - $total_break);
	return $hr_rate;
}

function daily_rate($total_sal){
	$daily_rate = ($total_sal * 12) / 365;
	return round($daily_rate,2);
}

function converToTime($time){
	$time = explode(':', $time);
	return round($time[0]*3600 + $time[1] * 60);
}

function mins($time){
	if($time != ""){
		$time = explode(':',$time);
		return ((int)$time[0] * 60) + (int)$time[1];
	}else{
		return 0;
	}
}

function number($num_format){
	$num = filter_var($num_format, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	return $num;
}

function clean_string($string){
	$string = str_replace("'", "",$string);
	return $string;
}

function replace($number, $char = "x"){
	return $number = preg_replace('/[A-Za-z0-9,.]/',$char, $number);
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function sortArray($a, $b, $column){
		$a = $a["' . $column . '"];
		$b = $b["' . $column . '"];

    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
}

function truncateHtml($text, $length = 500, $ending = '...', $exact = false, $considerHtml = true, $rmv_img = true) {
	if ($considerHtml) {
		// if the plain text is shorter than the maximum length, return the whole text
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach ($lines as $line_matchings) {
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
					unset($open_tags[$pos]);
					}
				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length) {
				break;
			}
		}
	} else {
		if (strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}
	// if the words shouldn't be cut in the middle...
	if (!$exact) {
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		if (isset($spacepos)) {
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
	}
	// add the defined ending to the text
	$truncate .= $ending;
	if($considerHtml) {
		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}
	}

	if($rmv_img){
		$truncate = preg_replace("/<img[^>]+\>/i", "(image) ", $truncate);
	}

	return $truncate;
}

function hr_id(){
	$conn =& get_instance();
	$dept = $conn->model->get_dept_by_type(2);
	return ($dept->num_rows() > 0) ? $dept->row()->departmentid : 1;
	// return 1;
}

function accounting_id(){
	$conn =& get_instance();
	$dept = $conn->model->get_dept_by_type(3);
	return ($dept->num_rows() > 0) ? $dept->row()->departmentid : 3;
}

function hr_or_above(){
	return 2;
}

function hr_sup_or_above(){
	return 3;
}

function regular_employee(){
	return 1;
}

function supervisor_and_above(){
	return 5;
}

function find_match($find,$string){
	if(preg_match("/".$find."/",$string)){
		return true;
	}else{
		return false;
	}
}

function in_range($num,$a,$b){
	if(in_array($num,range($a,$b))){
		return true;
	}else{
		return false;
	}
}

function decrypt_array($arr){
	$arr2 = array();
	foreach($arr as $row){
		$arr2[] = "'".en_dec('dec',$row)."'";
	}
	return $arr2;
}

function check_func_access($json,$id){
	$parse = json_decode($json);
	// return $parse;
	$ids = [];
	foreach($parse as $nav){
		if((int)$nav->id == (int)$id){
			$ids = $nav->access_func_nav;
		}
	}
	return $ids;
}

function approve_access($arr){
	if(count(($arr)) == 0){
		return false;
	}
	if(in_array("6",$arr)){
		return true;
	}else{
		return false;
	}
}

function certify_access($arr){
	if(count(($arr)) == 0){
		return false;
	}
	if(in_array("7",$arr)){
		return true;
	}else{
		return false;
	}
}

function reject_access($arr){
	if(count(($arr)) == 0){
		return false;
	}
	if(in_array("8",$arr)){
		return true;
	}else{
		return false;
	}
}

### Marky End ###

### Renren ###
//convert HH:MM to mins
function convert_to_minutes($time){
	$time_explode = explode(':', $time);
	$converted_time = ($time_explode[0] * 60.0 + $time_explode[1] * 1.0);
	return $converted_time;
}
function convert_to_hours($time){
	$time_explode = explode(':', $time);
	$converted_time = round(($time_explode[0] * 60.0 + $time_explode[1] * 1.0)/60,0);
	return $converted_time;
}

function fix_schedule_hours($time_out,$time_in,$break_start,$break_end){ //schedule out, schedule in, break start, break end
	$break = $break_end - $break_start;
	$time  = $time_out - $time_in;
	$fsh = $time - $break;

	return $fsh;
}
//$schedtype,$lunch_deduction_base,$first_timeout,$sched_break_start,$mtti_minutes,$sched_break_end
//this needs increment parameter outside the function
function get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog){
	//------------------$gc_end_break - lunch start
	//-------------------$gc_start_break - lunch end
	//--baligtad
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
		$ded1 = 0;
		$ded2 = 0;
		$fix_lunch_break = 0;
		$lunch_deduction = 0;
		//-----------LUNCH DEDUCTION / OVERBREAK--------------------------
		//early out
		if($gc_start_break <= $sched_break_start){
			$x = $gc_start_break - $sched_break_start;
			$ded1 = $ded1 + $ded2;
		}
		//late in
		if($gc_end_break >= $sched_break_end){
			$y = $gc_end_break -$sched_break_end;
			$ded2 = $ded2 + $y;
		}
		//checks if start or end break of employee is greater than his actual time out
		if($gc_start_break > $sched_to || $gc_end_break > $sched_to){
			$lunch_deduction = 0;
		}else{
			//
			$lunch_deduction = $ded1 + $ded2;
		}
		// $lunch_deduction = $ded1 + $ded2;

		// $one = date('H:i', mktime(0,$gc_start_break));
		// $two = date('H:i', mktime(0,$start_break_partner));
		// $three = date('H:i', mktime(0,$gc_end_break));
		// $four = date('H:i', mktime(0,$end_break_partner));
		// print_r(array(
		// 	'start_break' => $one,
		// 	'start_partner' => $two,
		// 	'end_break' => $three,
		// 	'end_break_partner' => $four
		// ));


		// print_r(array('start' => $start_break_partner, 'sched' => $sched_break_start));
		//----FIX LUNCH BREAK------------------
		if(($gc_start_break > $sched_break_start) && ($start_break_partner < $sched_break_start)){
			$fix_lunch_break = $fix_lunch_break + ($gc_start_break - $sched_break_start);
		}
		else{
			$fix_lunch_break = $fix_lunch_break + 0;
		}
		if(($gc_end_break < $sched_break_end)){
			$fix_lunch_break = $fix_lunch_break + ($sched_break_end - $gc_end_break);
		}else{
			$fix_lunch_break = $fix_lunch_break + 0;
		}
		//checks if employee is half day
		if(($actual_timeout == $gc_start_break) && ($actual_timeout < $sched_break_end)){
			$fix_lunch_break = $fix_lunch_break	+ ($gc_start_break - $gc_end_break);
		}
		//checks if start or end break of employee is greater than his actual time out
		if($gc_start_break > $sched_to || $gc_end_break > $sched_to){
			$fix_lunch_break = 0;
		}else{
			$fix_lunch_break = $fix_lunch_break;
		}
		//will check if time record exist in work order. if workorder exist, it will remove the overbreak of employee
		$breakout_hhmm = date('H:i', mktime(0,$gc_start_break));
		$check_workorder = $trs->Timerecordsummary_model->check_lunch_workorder($employee_idno,$date_timelog,$breakout_hhmm)->row();
		if($check_workorder != null){
			$et = convert_to_minutes($check_workorder->end_time);
			if($et > $sched_break_end){
				$overbreak_deduct = $et - $sched_break_end;
				$fix_lunch_break =  $fix_lunch_break - $lunch_deduction;
				$lunch_deduction = $lunch_deduction - $overbreak_deduct;

			}
		}
		$getbreaks = array(
			'fix_lunch_break' => $fix_lunch_break,
			'lunch_deduction' => $lunch_deduction
		);
		return $getbreaks;
}
function gethalfday_mins_single($actual_timeout,$actual_timein,$sched_break_start,$sched_break_end,$sched_break_mins,$totalminutes,$sched_ti){
	//lunch computation for fix and halfday
	$totalminutes = ($actual_timeout - $actual_timein) - $sched_break_mins;

	//half day to
	if($actual_timeout <= $sched_break_start){
		//maaga pumasok
		if($actual_timein < $sched_ti){
			$totalminutes = $actual_timeout - $sched_ti;
		}else{
			$totalminutes = $actual_timeout - $actual_timein;
		}
	}else if($actual_timeout >= $sched_break_start){
		if($actual_timeout <= $sched_break_end){
			//half day here
			$totalminutes = $sched_break_start - $actual_timein;
		}else{
			//not half day
			$totalminutes = ($actual_timeout - $sched_ti) - ($sched_break_end - $sched_break_start);
		}
	}else{
		$totalminutes = $totalminutes;
	}
	return $totalminutes;

}
function gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to){
	$late = 0;
	$overtime = 0;
	$undertime = 0;

	// if(is_array($lunch_deduction) && array_key_exists('total_deduction',$lunch_deduction)) {
	// 	$overbreak = $lunch_deduction['total_deduction'];
	// }else{
	// 	$overbreak = 0;
	// }
	// print_r($overbreak);
	//for computing late and undertime, subtract the exceeding hours - kapag sumobra pa din. edi may ot
	//early time controller
	if($sched_ti > $actual_timein){
		$totalminutes = $totalminutes - ($sched_ti - $actual_timein);
	}

	//late
	if($sched_ti < $actual_timein){

		$late = $actual_timein - $sched_ti;
	}

	//undertime
	if($totalminutes < $workingmins){
		if($late > 0){
			$x = $late + $totalminutes;
			if($x < $workingmins){
				$undertime = $workingmins - $x;
			}else{
				$undertime = 0;
			}
		}else{
			$undertime = $workingmins - $totalminutes;
		}
	}
	//overtime
	if($totalminutes > $workingmins){
		$overtime = $totalminutes - $workingmins;
	}

	//will make the total minutes, ot, ut balance
	$fix_sched_fixer = $totalminutes + $undertime + $late;
	if($fix_sched_fixer >= $workingmins){
		$totalminutes = $workingmins - ($undertime + $late);
	}

	//if total minutes is greater than 480 mins
	if($totalminutes > $workingmins){
		$totalminutes = $workingmins;
	}

	//converts total minutes to hours - manhours
	$manhours = round(($totalminutes / 60),2);

	$returnval = array(
		"overtime" => $overtime,
		"undertime" => $undertime,
		"totalminutes" =>$totalminutes,
		"manhours" =>$manhours,
		"late" => $late,
	);
// print_r('undertime'.$undertime);
	return $returnval;
}
function get_worksched($employee_idno,$date_timelog_day){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	$sched_availability = "";
	$sched_ti_raw = "";
	$sched_to_raw = "";
	$sched_break_end_raw = "";
	$sched_break_start_raw = "";
	//convert work schedule to mins
	$sched_ti = "";
	$sched_to = "";
	//break
	$sched_break_start = "";
	$sched_break_end = "";
	$sched_break_mins =  "";
	$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
	// print_r($worksched);
	$get_sched_day = json_decode($worksched->work_sched);
	// print_r($get_sched_day->mon);
	// die();
	$sched_ti_raw = "";
	$sched_to_raw = "";
	$sched_break_end_raw = "";
	$sched_break_start_raw = "";
	//convert work schedule to mins
	$sched_ti = "";
	$sched_to = "";
	//break
	$sched_break_start = "";
	$sched_break_end = "";
	$sched_break_mins =  "";


	if($date_timelog_day == 0){

		if(($get_sched_day->sun[0] != "") || ($get_sched_day->sun[1] != "") || ($get_sched_day->sun[4] != "") || ($get_sched_day->sun[3]))
			{
				$sched_availability = "with_worksched";

				$sched_ti_raw = $get_sched_day->sun[0];
				$sched_to_raw = $get_sched_day->sun[1];
				$sched_break_end_raw = $get_sched_day->sun[4];
				$sched_break_start_raw = $get_sched_day->sun[3];

				//convert work schedule to mins
				$sched_ti = convert_to_minutes($sched_ti_raw);
				$sched_to = convert_to_minutes($sched_to_raw);
				//break
				$sched_break_start = convert_to_minutes($sched_break_start_raw);
				$sched_break_end = convert_to_minutes($sched_break_end_raw);
				$sched_break_mins =  $sched_break_end - $sched_break_start;


			//
			}else{
				$sched_availability = "without_worksched";

				$sched_ti_raw = 0;
				$sched_to_raw = 0;
				$sched_break_end_raw = 0;
				$sched_break_start_raw = 0;

				//convert work schedule to mins
				$sched_ti = 0;
				$sched_to = 0;
				//break
				$sched_break_start = 0;
				$sched_break_end = 0;
				$sched_break_mins =  0;
			//check ko dito kung may workorder
			//kung may work order tas holiday tas 2.0
			}
	}
	else if($date_timelog_day == 1){
		if(($get_sched_day->mon[0] != "") || ($get_sched_day->mon[1] != "") || ($get_sched_day->mon[4] != "") || ($get_sched_day->mon[3]))
			{
				$sched_availability = "with_worksched";

				$sched_ti_raw = $get_sched_day->mon[0];
				$sched_to_raw = $get_sched_day->mon[1];
				$sched_break_end_raw = $get_sched_day->mon[4];
				$sched_break_start_raw = $get_sched_day->mon[3];

				//convert work schedule to mins
				$sched_ti = convert_to_minutes($sched_ti_raw);
				$sched_to = convert_to_minutes($sched_to_raw);
				//break
				$sched_break_start = convert_to_minutes($sched_break_start_raw);
				$sched_break_end = convert_to_minutes($sched_break_end_raw);
				$sched_break_mins =  $sched_break_end - $sched_break_start;

			}else{
				$sched_availability = "without_worksched";

				$sched_ti_raw = 0;
				$sched_to_raw = 0;
				$sched_break_end_raw = 0;
				$sched_break_start_raw = 0;

				//convert work schedule to mins
				$sched_ti = 0;
				$sched_to = 0;
				//break
				$sched_break_start = 0;
				$sched_break_end = 0;
				$sched_break_mins =  0;
			}
	}
	else if($date_timelog_day == 2){
		if(($get_sched_day->tue[0] != "") || ($get_sched_day->tue[1] != "") || ($get_sched_day->tue[4] != "") || ($get_sched_day->tue[3]))
		{
		$sched_availability = "with_worksched";

		$sched_ti_raw = $get_sched_day->tue[0];
		$sched_to_raw = $get_sched_day->tue[1];
		$sched_break_end_raw = $get_sched_day->tue[4];
		$sched_break_start_raw = $get_sched_day->tue[3];

		//convert work schedule to mins
		$sched_ti = convert_to_minutes($sched_ti_raw);
		$sched_to = convert_to_minutes($sched_to_raw);
		//break
		$sched_break_start = convert_to_minutes($sched_break_start_raw);
		$sched_break_end = convert_to_minutes($sched_break_end_raw);
		$sched_break_mins =  $sched_break_end - $sched_break_start;

		}else{
			$sched_availability = "without_worksched";

			$sched_ti_raw = 0;
				$sched_to_raw = 0;
				$sched_break_end_raw = 0;
				$sched_break_start_raw = 0;

				//convert work schedule to mins
				$sched_ti = 0;
				$sched_to = 0;
				//break
				$sched_break_start = 0;
				$sched_break_end = 0;
				$sched_break_mins =  0;
		}
	}
	else if($date_timelog_day == 3){
		if(($get_sched_day->wed[0] != "") || ($get_sched_day->wed[1] != "") || ($get_sched_day->wed[4] != "") || ($get_sched_day->wed[3]))
			{
				$sched_availability = "with_worksched";

				$sched_ti_raw = $get_sched_day->wed[0];
				$sched_to_raw = $get_sched_day->wed[1];
				$sched_break_end_raw = $get_sched_day->wed[4];
				$sched_break_start_raw = $get_sched_day->wed[3];

				//convert work schedule to mins
				$sched_ti = convert_to_minutes($sched_ti_raw);
				$sched_to = convert_to_minutes($sched_to_raw);
				//break
				$sched_break_start = convert_to_minutes($sched_break_start_raw);
				$sched_break_end = convert_to_minutes($sched_break_end_raw);
				$sched_break_mins =  $sched_break_end - $sched_break_start;

			}else{
				$sched_availability = "without_worksched";
				$sched_ti_raw = 0;
				$sched_to_raw = 0;
				$sched_break_end_raw = 0;
				$sched_break_start_raw = 0;

				//convert work schedule to mins
				$sched_ti = 0;
				$sched_to = 0;
				//break
				$sched_break_start = 0;
				$sched_break_end = 0;
				$sched_break_mins =  0;
			}
	}
	else if($date_timelog_day == 4){
		if(($get_sched_day->thu[0] != "") || ($get_sched_day->thu[1] != "") || ($get_sched_day->thu[4] != "") || ($get_sched_day->thu[3]))
		{
			$sched_availability = "with_worksched";

			$sched_ti_raw = $get_sched_day->thu[0];
			$sched_to_raw = $get_sched_day->thu[1];
			$sched_break_end_raw = $get_sched_day->thu[4];
			$sched_break_start_raw = $get_sched_day->thu[3];

			//convert work schedule to mins
			$sched_ti = convert_to_minutes($sched_ti_raw);
			$sched_to = convert_to_minutes($sched_to_raw);
			//break
			$sched_break_start = convert_to_minutes($sched_break_start_raw);
			$sched_break_end = convert_to_minutes($sched_break_end_raw);
			$sched_break_mins =  $sched_break_end - $sched_break_start;

		}else{
			$sched_availability = "without_worksched";

			$sched_ti_raw = 0;
				$sched_to_raw = 0;
				$sched_break_end_raw = 0;
				$sched_break_start_raw = 0;

				//convert work schedule to mins
				$sched_ti = 0;
				$sched_to = 0;
				//break
				$sched_break_start = 0;
				$sched_break_end = 0;
				$sched_break_mins =  0;
		}
	}
	else if($date_timelog_day == 5){
		if(($get_sched_day->fri[0] != "") || ($get_sched_day->fri[1] != "") || ($get_sched_day->fri[4] != "") || ($get_sched_day->fri[3]))
		{
			$sched_availability = "with_worksched";

			$sched_ti_raw = $get_sched_day->fri[0];
			$sched_to_raw = $get_sched_day->fri[1];
			$sched_break_end_raw = $get_sched_day->fri[4];
			$sched_break_start_raw = $get_sched_day->fri[3];

			//convert work schedule to mins
			$sched_ti = convert_to_minutes($sched_ti_raw);
			$sched_to = convert_to_minutes($sched_to_raw);
			//break
			$sched_break_start = convert_to_minutes($sched_break_start_raw);
			$sched_break_end = convert_to_minutes($sched_break_end_raw);
			$sched_break_mins =  $sched_break_end - $sched_break_start;

		}else{
			$sched_availability = "without_worksched";

			$sched_ti_raw = 0;
				$sched_to_raw = 0;
				$sched_break_end_raw = 0;
				$sched_break_start_raw = 0;

				//convert work schedule to mins
				$sched_ti = 0;
				$sched_to = 0;
				//break
				$sched_break_start = 0;
				$sched_break_end = 0;
				$sched_break_mins =  0;
		}
	}
	else if($date_timelog_day == 6){
		if(($get_sched_day->sat[0] != "") || ($get_sched_day->sat[1] != "") || ($get_sched_day->sat[4] != "") || ($get_sched_day->sat[3]))
		{
			// print_r("xxxxxxxxxxxxxxxxxxxx");
			$sched_availability = "with_worksched";

			$sched_ti_raw = $get_sched_day->sat[0];
			$sched_to_raw = $get_sched_day->sat[1];
			$sched_break_end_raw = $get_sched_day->sat[4];
			$sched_break_start_raw = $get_sched_day->sat[3];

			//convert work schedule to mins
			$sched_ti = convert_to_minutes($sched_ti_raw);
			$sched_to = convert_to_minutes($sched_to_raw);
			//break
			$sched_break_start = convert_to_minutes($sched_break_start_raw);
			$sched_break_end = convert_to_minutes($sched_break_end_raw);
			$sched_break_mins =  $sched_break_end - $sched_break_start;

		}else{
			$sched_availability = "without_worksched";

			$sched_ti_raw = 0;
				$sched_to_raw = 0;
				$sched_break_end_raw = 0;
				$sched_break_start_raw = 0;

				//convert work schedule to mins
				$sched_ti = 0;
				$sched_to = 0;
				//break
				$sched_break_start = 0;
				$sched_break_end = 0;
				$sched_break_mins =  0;
		}
	}

	$workschedval = array(
					'sched_availability' => $sched_availability,
					'sched_ti_raw' => $sched_ti_raw,
					'sched_to_raw' => $sched_to_raw,
					'sched_ti' => $sched_ti,
					'sched_to' => $sched_to,
					'sched_break_start' => $sched_break_start,
					'sched_break_end' => $sched_break_end,
					'sched_break_mins' => $sched_break_mins
						);
	return $workschedval;
}

function trs($employee_idno,$date_created){
	//THIS IS THE PAST FORMULA BEFORE SPLITTING FORMULAS. FOR REFERENCE ONLY
	//call of model.
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog($employee_idno,$date_created);
	$datesresult = $getdates->result();
	$datelength = $getdates->num_rows();
	//this will get all the time in and out of all the timelog that are not on trs
	//this will get all data from workorder that is not present in trs
	$alltimelog = array();
	$rescounter = 0;
	foreach($datesresult as $dr){
				$rescounter++;
				$employee_idno = $dr->employee_idno;
				$date_timelog = $dr->date;
				$date_timelog_day = date('w', strtotime($date_timelog));
				// $result    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date_timelog)));
				$workschedcounter = $trs->Timerecordsummary_model->countworkorder($employee_idno)->num_rows();
				if($workschedcounter > 0){
				//first time in
				$fti_data = $trs->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
				//last time out
				$lto_data = $trs->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
				//last time in
				$lti_data = $trs->Timerecordsummary_model->get_last_time_in($employee_idno,$date_timelog)->row();
				//first time out
				$fto_data = $trs->Timerecordsummary_model->get_first_time_out($employee_idno,$date_timelog)->row();
				$fti = $fti_data->time_in;
				$lto = $lto_data->time_out;
				$lti = $lti_data->time_in;
				$fto = $fto_data->time_out;
				//timelogcounterperday
				$multipletimelogcounter = $trs->Timerecordsummary_model->count_timelog($employee_idno,$date_timelog)->num_rows();
				//this will determine if schedule is fixed or flexi
				$st = $trs->Timerecordsummary_model->getschedtype($employee_idno)->row();
				$schedtype = $st->sched_type;
				//this will get the schedule of the employee on work schedule
				$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
				// $worksched_day = $worksched->work_sched;
				// print_r(array($lto,$fti));
				$timelogdata = array($employee_idno,$date_timelog,$fti,$lto);
				$alltimelog[] = $timelogdata;
				$get_sched_day = json_decode($worksched->work_sched);
				// print_r($get_sched_day->mon[]);
				//will get the schedule of employee - days
					//constant values
					$workingmins = 480;
					$breakmins = 60;
					$late = 0;
					$undertime = 0;
					$overtime = 0;
					$absent = 0;
					$manhours = 0;
				//----------------------Will determine the workschedule of the database-------------------------------
				//----------------------Will also provide sched and convert to minutes--------------------------------
				if($date_timelog_day == 0){

					if(($get_sched_day->sun[0] != "") || ($get_sched_day->sun[1] != "") || ($get_sched_day->sun[4] != "") || ($get_sched_day->sun[3]))
					{
					$sched_availability = "with_worksched";

					$sched_ti_raw = $get_sched_day->sun[0];
					$sched_to_raw = $get_sched_day->sun[1];
					$sched_break_end_raw = $get_sched_day->sun[4];
					$sched_break_start_raw = $get_sched_day->sun[3];

					//convert work schedule to mins
					$sched_ti = convert_to_minutes($sched_ti_raw);
					$sched_to = convert_to_minutes($sched_to_raw);
					//break
					$sched_break_start = convert_to_minutes($sched_break_start_raw);
					$sched_break_end = convert_to_minutes($sched_break_end_raw);
					$sched_break_mins =  $sched_break_end - $sched_break_start;


					//
					}else{
					$sched_availability = "without_worksched";
					//check ko dito kung may workorder
					//kung may work order tas holiday tas 2.0
					}
				}
				else if($date_timelog_day == 1){
					if(($get_sched_day->mon[0] != "") || ($get_sched_day->mon[1] != "") || ($get_sched_day->mon[4] != "") || ($get_sched_day->mon[3]))
					{
					$sched_availability = "with_worksched";

					$sched_ti_raw = $get_sched_day->mon[0];
					$sched_to_raw = $get_sched_day->mon[1];
					$sched_break_end_raw = $get_sched_day->mon[4];
					$sched_break_start_raw = $get_sched_day->mon[3];

					//convert work schedule to mins
					$sched_ti = convert_to_minutes($sched_ti_raw);
					$sched_to = convert_to_minutes($sched_to_raw);
					//break
					$sched_break_start = convert_to_minutes($sched_break_start_raw);
					$sched_break_end = convert_to_minutes($sched_break_end_raw);
					$sched_break_mins =  $sched_break_end - $sched_break_start;

					}else{
					$sched_availability = "without_worksched";

					}
				}
				else if($date_timelog_day == 2){
					if(($get_sched_day->tue[0] != "") || ($get_sched_day->tue[1] != "") || ($get_sched_day->tue[4] != "") || ($get_sched_day->tue[3]))
					{
					$sched_availability = "with_worksched";

					$sched_ti_raw = $get_sched_day->tue[0];
					$sched_to_raw = $get_sched_day->tue[1];
					$sched_break_end_raw = $get_sched_day->tue[4];
					$sched_break_start_raw = $get_sched_day->tue[3];

					//convert work schedule to mins
					$sched_ti = convert_to_minutes($sched_ti_raw);
					$sched_to = convert_to_minutes($sched_to_raw);
					//break
					$sched_break_start = convert_to_minutes($sched_break_start_raw);
					$sched_break_end = convert_to_minutes($sched_break_end_raw);
					$sched_break_mins =  $sched_break_end - $sched_break_start;

					}else{
					$sched_availability = "without_worksched";
					}
				}
				else if($date_timelog_day == 3){
					if(($get_sched_day->wed[0] != "") || ($get_sched_day->wed[1] != "") || ($get_sched_day->wed[4] != "") || ($get_sched_day->wed[3]))
					{
					$sched_availability = "with_worksched";

					$sched_ti_raw = $get_sched_day->wed[0];
					$sched_to_raw = $get_sched_day->wed[1];
					$sched_break_end_raw = $get_sched_day->wed[4];
					$sched_break_start_raw = $get_sched_day->wed[3];

					//convert work schedule to mins
					$sched_ti = convert_to_minutes($sched_ti_raw);
					$sched_to = convert_to_minutes($sched_to_raw);
					//break
					$sched_break_start = convert_to_minutes($sched_break_start_raw);
					$sched_break_end = convert_to_minutes($sched_break_end_raw);
					$sched_break_mins =  $sched_break_end - $sched_break_start;

					}else{
					$sched_availability = "without_worksched";
					}
				}
				else if($date_timelog_day == 4){
					if(($get_sched_day->thu[0] != "") || ($get_sched_day->thu[1] != "") || ($get_sched_day->thu[4] != "") || ($get_sched_day->thu[3]))
					{
					$sched_availability = "with_worksched";

					$sched_ti_raw = $get_sched_day->thu[0];
					$sched_to_raw = $get_sched_day->thu[1];
					$sched_break_end_raw = $get_sched_day->thu[4];
					$sched_break_start_raw = $get_sched_day->thu[3];

					//convert work schedule to mins
					$sched_ti = convert_to_minutes($sched_ti_raw);
					$sched_to = convert_to_minutes($sched_to_raw);
					//break
					$sched_break_start = convert_to_minutes($sched_break_start_raw);
					$sched_break_end = convert_to_minutes($sched_break_end_raw);
					$sched_break_mins =  $sched_break_end - $sched_break_start;

					}else{
					$sched_availability = "without_worksched";
					}
				}
				else if($date_timelog_day == 5){
					if(($get_sched_day->fri[0] != "") || ($get_sched_day->fri[1] != "") || ($get_sched_day->fri[4] != "") || ($get_sched_day->fri[3]))
					{
					$sched_availability = "with_worksched";

					$sched_ti_raw = $get_sched_day->fri[0];
					$sched_to_raw = $get_sched_day->fri[1];
					$sched_break_end_raw = $get_sched_day->fri[4];
					$sched_break_start_raw = $get_sched_day->fri[3];

					//convert work schedule to mins
					$sched_ti = convert_to_minutes($sched_ti_raw);
					$sched_to = convert_to_minutes($sched_to_raw);
					//break
					$sched_break_start = convert_to_minutes($sched_break_start_raw);
					$sched_break_end = convert_to_minutes($sched_break_end_raw);
					$sched_break_mins =  $sched_break_end - $sched_break_start;

					}else{
					$sched_availability = "without_worksched";
					}
				}
				else if($date_timelog_day == 6){
					if(($get_sched_day->sat[0] != "") || ($get_sched_day->sat[1] != "") || ($get_sched_day->sat[4] != "") || ($get_sched_day->sat[3]))
					{
					// print_r("xxxxxxxxxxxxxxxxxxxx");
					$sched_availability = "with_worksched";

					$sched_ti_raw = $get_sched_day->sat[0];
					$sched_to_raw = $get_sched_day->sat[1];
					$sched_break_end_raw = $get_sched_day->sat[4];
					$sched_break_start_raw = $get_sched_day->sat[3];

					//convert work schedule to mins
					$sched_ti = convert_to_minutes($sched_ti_raw);
					$sched_to = convert_to_minutes($sched_to_raw);
					//break
					$sched_break_start = convert_to_minutes($sched_break_start_raw);
					$sched_break_end = convert_to_minutes($sched_break_end_raw);
					$sched_break_mins =  $sched_break_end - $sched_break_start;

					}else{
					$sched_availability = "without_worksched";

					}
				}
				//convert actual timein/out to  minutes
				//first time in:
				$actual_timein = convert_to_minutes($fti);
				//last time out:
				$actual_timeout = convert_to_minutes($lto);
				//first time out
				$first_timeout = convert_to_minutes($fto);
				//last time in
				$last_timein = convert_to_minutes($lti);
				//--------------------Will check first the schedule availability of employee-----------------
				switch($sched_availability){
				//-------------------------------------------------------------------------------------------
				//----------------------------------------------Employee has work schedule-------------------
				case "with_worksched": //check if with work schedule
				$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();

				//---------------------------------Checking of Work Order---------------------------------------

				//------------------------------EMPLOYEE HAS WORK ORDER-----------------------------------------
				//-------------------------------------------------------------------------------------------------

				if($getworkorder != null){
					//checks if employee first time in > $sched_break_end
					if($schedtype == 'fix'){
						//checks if employee first time in > $end schedule
						$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
						if($check_endshift_timelog == "too_late"){
							continue;
						}
						//checks if employee last time in < $sched_break_start
						$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
						if($check_startshift_timelog == "too_early"){
							continue;
						}
					}
					//checks if employee first time in > first_timeout
					if($actual_timein > $actual_timeout){
						continue;
					}
					//if  multiple timelog
						if($multipletimelogcounter > 1){
							$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
							$total_minutes_base= 0;
							$lunch_deduction_base = 0;
							$lunch_deduction = 0;
							//titignan nya kung may time out per timelog is > sched time out. kapag yung time in nya mas mababa sa sched time out, may computations. else, may computation. di maapektuhan OT dahil chine check lang nya yung time_out > $sched_to && time_in <$sched_to.
							$fix_workmins_controller = 0;
							$alltimeout = array();
							$alltimein = array();
							//this will looop and count all timelogs of employees
							foreach($get_multiple_timelog as $gmt){
								$mt_timeout = $gmt->time_out;
								$mt_timein = $gmt->time_in;
								//converts mo minutes
								//time out
								$mtto_mins = convert_to_minutes($mt_timeout);
								//time in
								$mtti_mins = convert_to_minutes($mt_timein);

								array_push($alltimeout,$mtto_mins);
								array_push($alltimein,$mtti_mins);
								if($schedtype == 'fix'){
									if($mtto_mins > $sched_to && $mtti_mins < $sched_to)
									{
										$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
									}
								}
								//$schedtype,$lunch_deduction_base,$first_timeout,$sched_break_start,$mtti_minutes,$sched_break_end
								$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
								// $lunch_deduction_base++;
							}

							$totalminutes = $total_minutes_base;
							$lto = $getworkorder->end_time;
							//will add workorder on totalminutes of employee
							$tm = $totalminutes;
							if($schedtype == 'fix'){
								//break computations
								$gc_start_break = getClosest($sched_break_start,$alltimeout);
								$key = array_search($gc_start_break,$alltimeout);
								$start_break_partner = $alltimein[$key];
								$gc_end_break = getClosest($sched_break_end,$alltimein);
								//returns the break greater thank the start break and prevents returning two equal results
								// if($gc_end_break < $gc_start_break){
								// 	$less_than_to_mins = array_search($gc_end_break,$alltimein);
								// 	unset($alltimein[$less_than_to_mins]);
								// }
								// $gc_end_break = getClosest($sched_break_end,$alltimein);
								$key2 = array_search($gc_end_break,$alltimein);
								$end_break_partner = $alltimeout[$key2];
								$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
								$totalminutes = $totalminutes - $lunch_deduction['fix_lunch_break'] - $fix_workmins_controller;
								//overtime
								$workingmins = fix_schedule_hours($sched_to,$sched_ti,$sched_break_start,$sched_break_end);
								$workingmins = negative_checker($workingmins);
								$overtime =  gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['overtime'];
								$overtime = negative_checker($overtime);
								$undertime = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['undertime'];
								$undertime = negative_checker($undertime);
								$manhours =  gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['manhours'];
								$manhours = negative_checker($manhours);
								$totalminutes = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['totalminutes'];
								$totalminutes = negative_checker($totalminutes);
								$late = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['late'];
								$late = negative_checker($late);
								$overbreak = $lunch_deduction['lunch_deduction'];
								$overbreak = negative_checker($overbreak);
							}
							else if($schedtype == 'flexi'){
								//-------COMPUTATION FOR FLEXI-------
								$overbreak = 0;
								$totalminutes = $totalminutes - $breakmins;
								$totalminutes = negative_checker($totalminutes);
								//overtime
								if($totalminutes > $workingmins){
									$overtime = $totalminutes - $workingmins;
								}else{
									$overtime = 0;
								}
								$overtime = negative_checker($overtime);
								//undertime
								if($totalminutes < $workingmins){
									$undertime = $workingmins - $totalminutes;
								}else{
									$undertime = 0;
								}
								$undertime = negative_checker($undertime);
								//totalminutes
								if($totalminutes > $workingmins){
									$totalminutes = $workingmins;
								}
								$manhours = round(($totalminutes / 60),2);
							}
							else{
								$totalminutes = 0;
							}
						}
						//----------------------------single timelog with workorder-----------------------------
						//--------------------------------------------------------------------------------------

						else{
						//will check if time in = time out
						if($actual_timeout == $actual_timein){
							continue;
						}
						//will void time in and out greater than sched_to
						if($sched_to < $actual_timein){
							continue;
						}
						if($schedtype == "fix"){
							//work order values
							$overtime = 0;
							$undertime = 0;
							$late = 0;
							$overbreak = 0;
							//checks if actual_timein is too early
							if($sched_ti > $actual_timein){
								$actual_timein = $sched_ti;
							}
							$totalminutes = ($actual_timeout - $actual_timein)  - $sched_break_mins;
							$totalminutes = gethalfday_mins_single($actual_timeout,$actual_timein,$sched_break_start,$sched_break_end,$sched_break_mins,$totalminutes,$sched_ti);
							$totalminutes = negative_checker($totalminutes);
							//late
							if($sched_ti < $actual_timein){
								$late = $actual_timein - $sched_ti;
							}
							$late = negative_checker($late);
							//undertime
							if($late > 0 ){ //late dumating tapos maaga umuwi
								if($actual_timeout < $sched_to){
									$undertime = $workingmins - ($totalminutes + $late);
								}
							}else{
								if($sched_ti <= $actual_timein){
									if($actual_timeout <= $sched_to){
										$undertime = $workingmins - $totalminutes;
									}else{
										$undertime = $actual_timein - $sched_ti;
									}
								}
							}
							$undertime = negative_checker($undertime);
							//overtime
							if(($actual_timein <= $sched_ti) && ($actual_timeout >= $sched_to)){
								$overtime = $totalminutes - $workingmins;
							}
							$overtime = negative_checker($overtime);
							$fix_sched_fixer = $totalminutes + $undertime + $late;
							if($fix_sched_fixer >= $workingmins){
								$totalminutes = $workingmins - ($undertime + $late);
							}
							if($totalminutes >= $workingmins){
								$totalminutes = $workingmins;
							}
							$manhours = round(($totalminutes / 60),2);
						}
						else if($schedtype	== "flexi"){
							$overbreak = 0;
							$totalminutes = ($actual_timeout - $actual_timein)  - $breakmins;
							$totalminutes = negative_checker($totalminutes);
							//overtime
							if($totalminutes > $workingmins){
								$overtime = $totalminutes - $workingmins;
							}else{
								$overtime = 0;
							}
							$overtime = negative_checker($overtime);
							//undertime
							if($totalminutes < $workingmins){
								$undertime = $workingmins - $totalminutes;
							}else{
								$undertime = 0;
							}
							$undertime = negative_checker($undertime);
							//no late for flexi
							//totalminutes
							if($totalminutes > $workingmins){
								$totalminutes = $workingmins;
							}
							//manhours
							if($totalminutes > $workingmins){
								$totalminutes = $workingmins;
							}
								$manhours = round(($totalminutes / 60),2);
						}
						else{
							$totalminutes = 0;
							}
						}

						//----------------------------CHECKING OF WORKORDER--------------------------------------
						//---------------------------------------------------------------------------------------
						$fti_workorder = convert_to_minutes($getworkorder->start_time);
						$lto_workorder = convert_to_minutes($getworkorder->end_time);
						$cpw = $trs->Timerecordsummary_model->check_post_workorder($employee_idno,$date_timelog,$lto)->row();
						if($cpw != null){
							$check_post_workorder = convert_to_minutes($cpw->start_time);
						}else{
							$check_post_workorder = 0;
						}
						//2 is the equivalent of 'With Workorder'
						$remarks = 2;
						// $wo_combine = $lto_workorder - $fti_workorder;
						if($late > 0 || $undertime > 0){
						//checking of late
							if($late > 0){
								if($fti_workorder <= $actual_timein){
									$wo_combine = $trs->Timerecordsummary_model->get_pre_workorder($employee_idno,$date_timelog,$actual_timein);
									if($late > $wo_combine){
										$late = $late - $wo_combine;
									}else{
										//ifi fill mo yung total minutes ng wo_combine para dumagdag sa total minutes
										$wo_combine = $late;
										$late = 0;
									}
									// $late = $late - $wo_combine;
									$totalminutes = $totalminutes + $wo_combine	;
									$manhours  = round(($totalminutes / 60),2);
									//if workorder > late - pang chek ng negative
									if($late < 0){
										$late = 0;
									}else{
										$late = $late;
									}
								}
							}
						//checking of undertime
						if($undertime > 0 ){
							if($check_post_workorder >= $actual_timeout){
								$wo_combine = $trs->Timerecordsummary_model->get_post_workorder($employee_idno,$date_timelog,$actual_timeout);
								if($undertime > $wo_combine){
									$undertime = $undertime - $wo_combine;
								}else{
									$wo_combine = $undertime;
									$undertime = 0;
								}
								// $undertime  = $undertime - $wo_combine;
								$totalminutes = $totalminutes + $wo_combine;
								$manhours = round(($totalminutes / 60),2);
								//if workorder > undertime - pang chek ng negative
								if($undertime < 0){
									$undertime = 0;
								}else{
									$undertime = $undertime;
								}
							}
						}
						}else{
							if($fti_workorder > $actual_timeout){
							$overtime = $overtime + $wo_combine;
							}
							$late = $late;
							$undertime = $undertime;
						}

					//checking of overtime
					$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
					if($check_ot != null){
						$ot_temp = $overtime;
						$overtime = check_overtime($ot_temp,$check_ot->minutes_of_overtime);
					}else{
						$overtime = 0;
						}
					// $insertdata = $trs->Timerecordsummary_model->insertdata_workorder($employee_idno,$date_timelog,$fti,$lto,$late,$overtime,$undertime,$absent,$totalminutes,$manhours,$overbreak,$remarks);
					$insertdata = array(
						'employee_idno' => $employee_idno,
						'date_created' => $date_timelog,
						'time_in' => $fti,
						'time_out' =>$lto,
						'late' => $late,
						'overtime' => $overtime,
						'undertime' => $undertime,
						'absent' => $absent,
						'totalminutes' => $totalminutes,
						'manhours' => $manhours,
						'overbreak' => $overbreak,
						'remarks' => $remarks);

				//------------------------------EMPLOYEE HAS NO WORK ORDER-----------------------------------------
				//-------------------------------------------------------------------------------------------------
				}
				else{ //start else
					if($schedtype == 'fix'){
						//checks if employee first time in > $end_schedule
						$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
						if($check_endshift_timelog == "too_late"){
							continue;
						}
						// checks if employee last time in < $sched_start
						$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
						if($check_startshift_timelog == "too_early"){
							continue;
						}
					}
					//checks if employee first time in > first_timeout
					if($actual_timein > $actual_timeout){
						continue;
					}
					//if  multiple timelog
					if($multipletimelogcounter > 1){
						$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
						$total_minutes_base= 0;
						//this will get exceeding hours
						$fix_workmins_controller = 0;

						// $lunch_deduction_base = 0;
						$lunch_deduction = 0;
						$alltimeout = array();
						$alltimein = array();

						//this will looop and count all timelogs of employees
						foreach($get_multiple_timelog as $gmt){
							$mt_timeout = $gmt->time_out;
							$mt_timein = $gmt->time_in;
							//converts mo minutes
							//time out
							$mtto_mins = convert_to_minutes($mt_timeout);
							//time in
							$mtti_mins = convert_to_minutes($mt_timein);

							$at_val = array(
								'in' => $mtti_mins,
								'out' => $mtto_mins
							);

							array_push($alltimeout,$mtto_mins);
							array_push($alltimein,$mtti_mins);
							//will check fix schedule if exceeded on sched time out
							if($schedtype == 'fix'){
								if($mtto_mins > $sched_to)
								{
									if($mtti_mins < $sched_to){
										$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
									}else if($mtti_mins > $sched_to){
										$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $mtti_mins);
									}
								}
							}
							$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
							// print_r($lunch_deduction_base);
						}

						$totalminutes = $total_minutes_base;
						$tm = $totalminutes;
						if($schedtype == 'fix'){
							//end is start break. start is end break
							$gc_start_break = getClosest($sched_break_start,$alltimeout);
							$key = array_search($gc_start_break,$alltimeout);
							$start_break_partner = $alltimein[$key];
							$gc_end_break = getClosest($sched_break_end,$alltimein);
							//returns the break greater thank the start break and prevents returning two equal results
							// if($gc_end_break < $gc_start_break){
							// 	$less_than_to_mins = array_search($gc_end_break,$alltimein);
							// 	unset($alltimein[$less_than_to_mins]);
							// }
							// $gc_end_break = getClosest($sched_break_end,$alltimein);
							$key2 = array_search($gc_end_break,$alltimein);
							$end_break_partner = $alltimeout[$key2];

							$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
							//subtract fix lunch break
							$totalminutes = $totalminutes - $lunch_deduction['fix_lunch_break'] - $fix_workmins_controller;
							$workingmins = fix_schedule_hours($sched_to,$sched_ti,$sched_break_start,$sched_break_end);
							$workingmins = negative_checker($workingmins);
							$overtime =  gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['overtime'];
							$overtime = negative_checker($overtime);
							$undertime = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['undertime'];
							$undertime = negative_checker($undertime);
							$manhours =  gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['manhours'];
							$manhours = negative_checker($manhours);
							$totalminutes = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['totalminutes'];
							$totalminutes = negative_checker($totalminutes);
							$late = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['late'];
							$late = negative_checker($late);
							$overbreak = $lunch_deduction['lunch_deduction'];
							$overbreak = negative_checker($overbreak);
						}
						else if($schedtype == 'flexi'){
						//-------COMPUTATION FOR FLEXI-------
						$overbreak = 0;
						$totalminutes = $totalminutes - $breakmins;
						$totalminutes = negative_checker($totalminutes);
						//overtime
						if($totalminutes > $workingmins){
						$overtime = $totalminutes - $workingmins;
						}else{
						$overtime = 0;
						}
						$overtime = negative_checker($overtime);
						//undertime
						if($totalminutes < $workingmins){
						$undertime = $workingmins - $totalminutes;
						}else{
						$undertime = 0;
						}
						$undertime = negative_checker($undertime);
						//totalminutes
						if($totalminutes > $workingmins){
							$totalminutes = $workingmins;
						}
							$manhours = round(($totalminutes / 60),2);
						}
					}
					else{
				//----------------------SINGLE COMPUTATIONS-----------------
				  //---------------------------------------------------------

					//will check if time in = time out
					if($actual_timeout == $actual_timein){
						continue;
					}
					//will void time in and out greater than sched_to
					if($sched_to < $actual_timein){
						continue;
					}
					if($schedtype == "fix"){
					$overtime = 0;
					$undertime = 0;
					$late = 0;
					$overbreak = 0;
					//lunch fixer for single fix timelog
					$totalminutes = ($actual_timeout - $actual_timein) - $sched_break_mins;
					$totalminutes = gethalfday_mins_single($actual_timeout,$actual_timein,$sched_break_start,$sched_break_end,$sched_break_mins,$totalminutes,$sched_ti);
					$totalminutes = negative_checker($totalminutes);
					//late
					if($sched_ti < $actual_timein){
						$late = $actual_timein - $sched_ti;
					}
					$late = negative_checker($late);
					//undertime

					if($late > 0 ){ //late dumating tapos maaga umuwi
						if($actual_timeout < $sched_to){
							$undertime = $workingmins - ($totalminutes + $late);
						}
					}else{
						if($sched_ti <= $actual_timein){
							if($actual_timeout <= $sched_to){
								$undertime = $workingmins - $totalminutes;
							}else{
								$undertime = $actual_timein - $sched_ti;
							}
						}else{
							$undertime = $workingmins - $totalminutes;
						}
					}
					$undertime = negative_checker($undertime);
					//overtime
					if(($actual_timein <= $sched_ti) && ($actual_timeout >= $sched_to)){
						$overtime = $totalminutes - $workingmins;
					}
					$overtime = negative_checker($overtime);
					$fix_sched_fixer = $totalminutes + $undertime + $late;

					//fix schedule fixer
					if($fix_sched_fixer >= $workingmins){
						$totalminutes = $workingmins - ($undertime + $late);
					}

					$totalminutes = $totalminutes;
					if($totalminutes >= $workingmins){
						$totalminutes = $workingmins;
					}
					$manhours = round(($totalminutes / 60),2);
					}


					else if($schedtype	== "flexi"){
					$overbreak = 0;
					$totalminutes = ($actual_timeout - $actual_timein) - $breakmins;
					$totalminutes = negative_checker($totalminutes);
					//overtime
					if($totalminutes > $workingmins){
						$overtime = $totalminutes - $workingmins;
					}else{
						$overtime = 0;
					}
					$overtime = negative_checker($totalminutes);
					//undertime
					if($totalminutes < $workingmins){
						$undertime = $workingmins - $totalminutes;
					}else{
						$undertime = 0;
					}
					$undertime = negative_checker($undertime);
					//no late for flexi
					//totalminutes
					if($totalminutes > $workingmins){
						$totalminutes = $workingmins;
					}
					//manhours
					if($totalminutes != 0){
						$totalhours = $totalminutes / 60;
						$manhours = round($totalhours,2);
					}else{
						$manhours = 0;
					}
					}
					}
					$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
					if($check_ot != null){
						$ot_temp = $overtime;
						$totalminutes = $totalminutes + $lunch_deduction['total_deduction'];
						$overtime = check_overtime($ot_temp,$check_ot->minutes_of_overtime);
					}else{
						$overtime = 0;
						}

					$insertdata = array(
						'employee_idno' => $employee_idno,
						'date_created' => $date_timelog,
						'time_in' => $fti,
						'time_out' =>$lto,
						'late' => $late,
						'overtime' => $overtime,
						'undertime' => $undertime,
						'absent' => $absent,
						'totalminutes' => $totalminutes,
						'manhours' => $manhours,
						'overbreak' => $overbreak);
				}
				break;
				//-------------------------------------------------------------------------------------------
				//----------------------------------------------Employee has work no schedule-------------------
				case "without_worksched";
				//if with work order
				$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
				//select all dates on workorder and convert it to days. then check it to time record summary if it exist
				//override the time in, time out that is in timerecordsummary_trial that is not in workorder
				if($getworkorder != null){
					$overbreak = 0;
					$fti = $getworkorder->start_time;
					$lto = $getworkorder->end_time;
					$actual_timein = convert_to_minutes($fti);
					//last time ou:
					// $lto_explode = explode(':', $lto);
					$actual_timeout = convert_to_minutes($lto);

					$totalminutes = ($actual_timeout - $actual_timein) - $breakmins;
					$totalminutes = negative_checker($totalminutes);
					// print_r($totalminutes."---");
						//overtime
					if($totalminutes > $workingmins){
						$overtime = $totalminutes - $workingmins;
					}else{
						$overtime = 0;
					}
					$overtime = negative_checker($overtime);
					//undertime
					if($totalminutes < $workingmins){
						$undertime = $workingmins - $totalminutes;
					}else{
							$undertime = 0;
					}
					$undertime = negative_checker($undertime);
					//totalminutes
					if($totalminutes > $workingmins){
						$totalminutes = $workingmins;
					}
					//manhours
					if($totalminutes != 0){
						$totalhours = $totalminutes / 60;
						$manhours = round($totalhours,2);
					}else{
						$manhours = 0;
					}
						//insert
					//will check if the overtime is approved, if not. no OT
					$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
					if($check_ot != null){
						$ot_temp = $overtime;
						$overtime = check_overtime($ot_temp,$check_ot->minutes_of_overtime);
					}else{
						$overtime = 0;
						}

					$insertdata = array(
						'employee_idno' => $employee_idno,
						'date_created' => $date_timelog,
						'time_in' => $fti,
						'time_out' =>$lto,
						'late' => $late,
						'overtime' => $overtime,
						'undertime' => $undertime,
						'absent' => $absent,
						'totalminutes' => $totalminutes,
						'manhours' => $manhours,
						'overbreak' => $overbreak);
					//will check if the overtime is approved, if not. no OT
				}

				else{
				}
				//else with
				break;
				}
				//convert hours to minutes
				//insert to database
				//timein,timeout,manhrs,late,ot,ut,absent,totalminutes
				// }
				}
				else{
				$noworksched = 0;
					$insertdata = array(
						'employee_idno' => $employee_idno,
						'date_created' => $date_timelog,
						'time_in' => $fti,
						'time_out' =>$lto,
						'late' => $noworksched,
						'overtime' => $noworksched,
						'undertime' => $noworksched,
						'absent' => $noworksched,
						'totalminutes' => $noworksched,
						'manhours' => $noworksched,
						'overbreak' => $noworksched,
						'remarks' => $noworksched);
				}
	}
}
//------------------Computations Breakdown---------------------------
//if purpose == time_recording, for time record summary, else purpose == others, for viewing of other modules
//put validations that will handle null. ex: if $compute_late == "" then employee is absent
function compute_late($employee_idno,$date_created,$purpose){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	if($purpose == "time_recording"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog($employee_idno,$date_created);
	}
	else if($purpose == "others"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}else{
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}

	$datesresult = $getdates->result();
	$datelength = $getdates->num_rows();
	//this will get all the time in and out of all the timelog that are not on trs
	//this will get all data from workorder that is not present in trs
	$alltimelog = array();
	$rescounter = 0;
	foreach($datesresult as $dr){
		$rescounter++;
		$employee_idno = $dr->employee_idno;
		$date_timelog = $dr->date;
		$date_timelog_day = date('w', strtotime($date_timelog));
		// $result    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date_timelog)));
		$workschedcounter = $trs->Timerecordsummary_model->countworkorder($employee_idno)->num_rows();
		if($workschedcounter > 0){
		//first time in
		$fti_data = $trs->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
		//last time out
		$lto_data = $trs->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
		//last time in
		$lti_data = $trs->Timerecordsummary_model->get_last_time_in($employee_idno,$date_timelog)->row();
		//first time out
		$fto_data = $trs->Timerecordsummary_model->get_first_time_out($employee_idno,$date_timelog)->row();
		$fti = $fti_data->time_in;
		$lto = $lto_data->time_out;
		$lti = $lti_data->time_in;
		$fto = $fto_data->time_out;
		//timelogcounterperday
		$multipletimelogcounter = $trs->Timerecordsummary_model->count_timelog($employee_idno,$date_timelog)->num_rows();
		//this will determine if schedule is fixed or flexi
		$st = $trs->Timerecordsummary_model->getschedtype($employee_idno)->row();
		$schedtype = $st->sched_type;
		//this will get the schedule of the employee on work schedule
		$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
		// $worksched_day = $worksched->work_sched;
		// print_r(array($lto,$fti));
		$timelogdata = array($employee_idno,$date_timelog,$fti,$lto);
		$alltimelog[] = $timelogdata;
		$get_sched_day = json_decode($worksched->work_sched);
		// print_r($get_sched_day->mon[]);
		//will get the schedule of employee - days
			//constant values
		$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
		$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$late = 0;
			$undertime = 0;
			$overtime = 0;
			$absent = 0;
			$manhours = 0;
		$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
		if($check_ot != null){
			$ot_mins = $check_ot->minutes_of_overtime;
		}else{
			$ot_mins = 0;
		}
		//----------------------Will determine the workschedule of the database-------------------------------
		//----------------------Will also provide sched and convert to minutes--------------------------------
		$get_worksched = get_worksched($employee_idno,$date_timelog_day);
		$sched_availability = $get_worksched['sched_availability'];
		$sched_ti_raw = $get_worksched['sched_ti_raw'];
		$sched_to_raw = $get_worksched['sched_to_raw'];
		$sched_ti = $get_worksched['sched_ti'];
		$sched_to = $get_worksched['sched_to'];
		$sched_break_start = $get_worksched['sched_break_start'];
		$sched_break_end = $get_worksched['sched_break_end'];
		$sched_break_mins = $get_worksched['sched_break_mins'];
		//convert actual timein/out to  minutes
		//first time in:
		$actual_timein = convert_to_minutes($fti);
		//last time out:
		$actual_timeout = convert_to_minutes($lto);
		//first time out
		$first_timeout = convert_to_minutes($fto);
		//last time in
		$last_timein = convert_to_minutes($lti);
		//--------------------Will check first the schedule availability of employee-----------------
		switch($sched_availability){
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work schedule-------------------
		case "with_worksched": //check if with work schedule
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
			//checks if employee first time in > $sched_break_end
			if($schedtype == 'fix'){
				//checks if employee first time in > $end schedule
				$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
				if($check_endshift_timelog == "too_late"){
					continue;
				}
				//checks if employee last time in < $sched_break_start
				$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
				if($check_startshift_timelog == "too_early"){
					continue;
				}
			}
			//checks if employee first time in > first_timeout
			if($actual_timein > $actual_timeout){
				continue;
			}
			//if  multiple timelog
				if($multipletimelogcounter > 1){
				$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
				$total_minutes_base= 0;
				//this will get exceeding hours
				$fix_workmins_controller = 0;
				// $lunch_deduction_base = 0;
				$lunch_deduction = 0;
				$alltimeout = array();
				$alltimein = array();
				//for checking of overlaps of ti and to
				$temp_out = 0;
				//checking of exceeding unfiled OT
				$ot_count = 0;
				//this will looop and count all timelogs of employees
				foreach($get_multiple_timelog as $gmt){
					$mt_timeout = $gmt->time_out;
					$mt_timein = $gmt->time_in;
					//converts mo minutes
					//time out
					$mtto_mins = convert_to_minutes($mt_timeout);
					//time in
					$mtti_mins = convert_to_minutes($mt_timein);
					//for checking of overlaps of ti and to
					if($mtti_mins < $temp_out){
						$mtti_mins = $temp_out;
					}
					$temp_out = $mtto_mins;
					//-----------------------------------
					$at_val = array(
						'in' => $mtti_mins,
						'out' => $mtto_mins
					);
					array_push($alltimeout,$mtto_mins);
					array_push($alltimein,$mtti_mins);
					//will check fix schedule if exceeded on sched time out
					if($schedtype == 'fix'){
						if($mtto_mins > $sched_to)
						{
							if($mtti_mins < $sched_to){
								$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
							}else if($mtti_mins >= $sched_to){
								if($check_ot != null){
									$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $mtti_mins);
								}else{
									//no OT filed
									$ot_count = $ot_count + ($mtto_mins - $mtti_mins);
									$fix_workmins_controller = $fix_workmins_controller + 0;
								}

							}
						}
					}
					$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
					// print_r($lunch_deduction_base);
				}
				$total_minutes_base = check_total_minutes_base($ot_mins,$ot_count,$total_minutes_base);
				$totalminutes = $total_minutes_base;
				$tm = $totalminutes;
				if($schedtype == 'fix'){
					//end is start break. start is end break
					$gc_start_break = getClosest($sched_break_start,$alltimeout);
					$key = array_search($gc_start_break,$alltimeout);
					$start_break_partner = $alltimein[$key];
					$gc_end_break = getClosest($sched_break_end,$alltimein);
					$key2 = array_search($gc_end_break,$alltimein);
					$end_break_partner = $alltimeout[$key2];

					$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
							//subtract fix lunch break
					$totalminutes = $totalminutes - $lunch_deduction['fix_lunch_break'] - $fix_workmins_controller;
					$workingmins = fix_schedule_hours($sched_to,$sched_ti,$sched_break_start,$sched_break_end);
					$workingmins = negative_checker($workingmins);
					$late = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['late'];
					$late = negative_checker($late);
					}
					else if($schedtype == 'flexi'){
						//-------COMPUTATION FOR FLEXI-------
						$late = 0;
					}
					else{
						$late = 0;
					}
				}
				//----------------------------single timelog with workorder-----------------------------
				//--------------------------------------------------------------------------------------
				else{
				//will check if time in = time out
				if($actual_timeout == $actual_timein){
					continue;
				}
				//will void time in and out greater than sched_to
				if($sched_to < $actual_timein){
					continue;
				}
				if($schedtype == "fix"){
					//work order values
					$late = 0;
					//late
					//checks if actual_timein is too early
					if($sched_ti > $actual_timein){
						$actual_timein = $sched_ti;
					}
					if($sched_ti < $actual_timein){
						$late = $actual_timein - $sched_ti;
					}
					$late = negative_checker($late);
				}
				else if($schedtype	== "flexi"){
					$late = 0;
				}
				else{
					$late = 0;
					}
				}

				//checking of remarks
				if($getworkorder != null){
					$remarks = 2;
				}else{
					$remarks = 0;
				}
				return $late;
		break;
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work no schedule-------------------
		case "without_worksched";
		//if with work order
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
		//select all dates on workorder and convert it to days. then check it to time record summary if it exist
		//override the time in, time out that is in timerecordsummary_trial that is not in workorder
		if($getworkorder != null){
			$late = 0;
		}

		else{
		}
				//else with
		break;
		}

		}
		else{
		$noworksched = 0;
		return $noworksched;
			}
		}

		if($purpose == "others"){
			$get_remaining_workorders = $trs->Timerecordsummary_model->get_all_workorders_others($employee_idno,$date_created);
			$get_remaining_workorders_result = $get_remaining_workorders->result();
			foreach($get_remaining_workorders_result as $g_r_w_r){
			$st = $g_r_w_r->start_time;
			$et = $g_r_w_r->end_time;
			$start_time = convert_to_minutes($st);
			$end_time = convert_to_minutes($et);
			$employee_idno = $g_r_w_r->employee_id;
			$date_timelog = $g_r_w_r->date;
			$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
			$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$late = 0;
			//start flexi computation
			$totalminutes = $end_time - $start_time;
			$totalminutes = $totalminutes - $breakmins;
			//totalminutes
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
						//equivalent to Day-off Workorder
			$remarks = 3;
			if($totalminutes > 0){
				return $late;
			}

			}
		}
}
function compute_overtime($employee_idno,$date_created,$purpose){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	if($purpose == "time_recording"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog($employee_idno,$date_created);
	}
	else if($purpose == "others"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}else{
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}
	$datesresult = $getdates->result();
	$datelength = $getdates->num_rows();
	//this will get all the time in and out of all the timelog that are not on trs
	//this will get all data from workorder that is not present in trs
	$alltimelog = array();
	$rescounter = 0;
	foreach($datesresult as $dr){
		$rescounter++;
		$employee_idno = $dr->employee_idno;
		$date_timelog = $dr->date;
		$date_timelog_day = date('w', strtotime($date_timelog));
		// $result    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date_timelog)));
		$workschedcounter = $trs->Timerecordsummary_model->countworkorder($employee_idno)->num_rows();
		if($workschedcounter > 0){
		//first time in
		$fti_data = $trs->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
		//last time out
		$lto_data = $trs->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
		//last time in
		$lti_data = $trs->Timerecordsummary_model->get_last_time_in($employee_idno,$date_timelog)->row();
		//first time out
		$fto_data = $trs->Timerecordsummary_model->get_first_time_out($employee_idno,$date_timelog)->row();
		$fti = $fti_data->time_in;
		$lto = $lto_data->time_out;
		$lti = $lti_data->time_in;
		$fto = $fto_data->time_out;
		//timelogcounterperday
		$multipletimelogcounter = $trs->Timerecordsummary_model->count_timelog($employee_idno,$date_timelog)->num_rows();
		//this will determine if schedule is fixed or flexi
		$st = $trs->Timerecordsummary_model->getschedtype($employee_idno)->row();
		$schedtype = $st->sched_type;
		//this will get the schedule of the employee on work schedule
		$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
		// $worksched_day = $worksched->work_sched;
		// print_r(array($lto,$fti));
		$timelogdata = array($employee_idno,$date_timelog,$fti,$lto);
		$alltimelog[] = $timelogdata;
		$get_sched_day = json_decode($worksched->work_sched);
		// print_r($get_sched_day->mon[]);
		//will get the schedule of employee - days
			//constant values
		$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
		$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$late = 0;
			$undertime = 0;
			$overtime = 0;
			$absent = 0;
			$manhours = 0;
		$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
		if($check_ot != null){
			$ot_mins = $check_ot->minutes_of_overtime;
		}else{
			$ot_mins = 0;
		}
		//----------------------Will determine the workschedule of the database-------------------------------
		//----------------------Will also provide sched and convert to minutes--------------------------------
		$get_worksched = get_worksched($employee_idno,$date_timelog_day);
		$sched_availability = $get_worksched['sched_availability'];
		$sched_ti_raw = $get_worksched['sched_ti_raw'];
		$sched_to_raw = $get_worksched['sched_to_raw'];
		$sched_ti = $get_worksched['sched_ti'];
		$sched_to = $get_worksched['sched_to'];
		$sched_break_start = $get_worksched['sched_break_start'];
		$sched_break_end = $get_worksched['sched_break_end'];
		$sched_break_mins = $get_worksched['sched_break_mins'];
		//convert actual timein/out to  minutes
		//first time in:
		$actual_timein = convert_to_minutes($fti);
		//last time out:
		$actual_timeout = convert_to_minutes($lto);
		//first time out
		$first_timeout = convert_to_minutes($fto);
		//last time in
		$last_timein = convert_to_minutes($lti);
		//--------------------Will check first the schedule availability of employee-----------------
		switch($sched_availability){
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work schedule-------------------
		case "with_worksched": //check if with work schedule
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
			//checks if employee first time in > $sched_break_end
			if($schedtype == 'fix'){
				//checks if employee first time in > $end schedule
				$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
				if($check_endshift_timelog == "too_late"){
					continue;
				}
				//checks if employee last time in < $sched_break_start
				$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
				if($check_startshift_timelog == "too_early"){
					continue;
				}
			}
			//checks if employee first time in > first_timeout
			if($actual_timein > $actual_timeout){
				continue;
			}
			//if  multiple timelog
				if($multipletimelogcounter > 1){
				$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
				$total_minutes_base= 0;
				//this will get exceeding hours
				$fix_workmins_controller = 0;
				// $lunch_deduction_base = 0;
				$lunch_deduction = 0;
				$alltimeout = array();
				$alltimein = array();

				//for checking of overlaps of ti and to
				$temp_out = 0;
				//checking of exceeding unfiled OT
				$ot_count = 0;
				//this will looop and count all timelogs of employees
				foreach($get_multiple_timelog as $gmt){
					$mt_timeout = $gmt->time_out;
					$mt_timein = $gmt->time_in;
					//converts mo minutes
					//time out
					$mtto_mins = convert_to_minutes($mt_timeout);
					//time in
					$mtti_mins = convert_to_minutes($mt_timein);

					//for checking of overlaps of ti and to
					if($mtti_mins < $temp_out){
						$mtti_mins = $temp_out;
					}
					$temp_out = $mtto_mins;
					//-----------------------------------
					$at_val = array(
						'in' => $mtti_mins,
						'out' => $mtto_mins
					);
					array_push($alltimeout,$mtto_mins);
					array_push($alltimein,$mtti_mins);
					//will check fix schedule if exceeded on sched time out
					if($schedtype == 'fix'){
						if($mtto_mins > $sched_to)
						{
							if($mtti_mins < $sched_to){
								$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
							}else if($mtti_mins >= $sched_to){
								if($check_ot != null){
									$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $mtti_mins);
								}else{
									//no OT filed
									$ot_count = $ot_count + ($mtto_mins - $mtti_mins);
									$fix_workmins_controller = $fix_workmins_controller + 0;
								}

							}
						}
					}
					$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
					// print_r($lunch_deduction_base);
				}
				$total_minutes_base = check_total_minutes_base($ot_mins,$ot_count,$total_minutes_base);
				$totalminutes = $total_minutes_base;
				$tm = $totalminutes;
				if($schedtype == 'fix'){
					//end is start break. start is end break
					$gc_start_break = getClosest($sched_break_start,$alltimeout);
					$key = array_search($gc_start_break,$alltimeout);
					$start_break_partner = $alltimein[$key];
					$gc_end_break = getClosest($sched_break_end,$alltimein);
					//returns the break greater thank the start break and prevents returning two equal results
					// if($gc_end_break < $gc_start_break){
					// 	$less_than_to_mins = array_search($gc_end_break,$alltimein);
					// 	unset($alltimein[$less_than_to_mins]);
					// }
					// $gc_end_break = getClosest($sched_break_end,$alltimein);
					$key2 = array_search($gc_end_break,$alltimein);
					$end_break_partner = $alltimeout[$key2];

					$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
							//subtract fix lunch break
					$totalminutes = $totalminutes - $lunch_deduction['fix_lunch_break'] - $fix_workmins_controller;
					$workingmins = fix_schedule_hours($sched_to,$sched_ti,$sched_break_start,$sched_break_end);
					$workingmins = negative_checker($workingmins);
					$overtime =  gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['overtime'];
					$overtime = negative_checker($overtime);
					}
					else if($schedtype == 'flexi'){
						//-------COMPUTATION FOR FLEXI-------
						$totalminutes = $totalminutes - $breakmins;
						$totalminutes = negative_checker($totalminutes);
						//overtime
						if($totalminutes > $workingmins){
							$overtime = $totalminutes - $workingmins;
						}else{
							$overtime = 0;
						}
					}
					else{
						$overtime = 0;
					}
				}
				//----------------------------single timelog with workorder-----------------------------
				//--------------------------------------------------------------------------------------
				else{
				//will check if time in = time out
				if($actual_timeout == $actual_timein){
					continue;
				}
				//will void time in and out greater than sched_to
				if($sched_to < $actual_timein){
					continue;
				}
				if($schedtype == "fix"){
					//work order values
					$overtime = 0;
					//checks if actual_timein is too early
					if($sched_ti > $actual_timein){
						$actual_timein = $sched_ti;
					}
					$totalminutes = ($actual_timeout - $actual_timein)  - $sched_break_mins;
					$totalminutes = gethalfday_mins_single($actual_timeout,$actual_timein,$sched_break_start,$sched_break_end,$sched_break_mins,$totalminutes,$sched_ti);
					$totalminutes = negative_checker($totalminutes);
					//overtime
					if(($actual_timein <= $sched_ti) && ($actual_timeout >= $sched_to)){
						$overtime = $totalminutes - $workingmins;
					}
					$overtime = negative_checker($overtime);
				}
				else if($schedtype	== "flexi"){
					$overbreak = 0;
					$totalminutes = ($actual_timeout - $actual_timein)  - $breakmins;
					$totalminutes = negative_checker($totalminutes);
					//overtime
					if($totalminutes > $workingmins){
						$overtime = $totalminutes - $workingmins;
					}else{
						$overtime = 0;
					}
					$overtime = negative_checker($overtime);
				}
				else{
						$overtime = 0;
					}
				}
			//checking of overtime
			$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
			if($check_ot != null){
				$ot_temp = $overtime;
				$overtime = check_overtime($ot_temp,$check_ot->minutes_of_overtime);
			}else{
				$overtime = 0;
				}

			return $overtime;
		break;
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work no schedule-------------------
		case "without_worksched";
		//if with work order
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
		//select all dates on workorder and convert it to days. then check it to time record summary if it exist
		//override the time in, time out that is in timerecordsummary_trial that is not in workorder
		if($getworkorder != null){
			$overbreak = 0;
			$fti = $getworkorder->start_time;
			$lto = $getworkorder->end_time;
			$actual_timein = convert_to_minutes($fti);
			//last time ou:
			// $lto_explode = explode(':', $lto);
			$actual_timeout = convert_to_minutes($lto);
			$totalminutes = ($actual_timeout - $actual_timein) - $breakmins;
			$totalminutes = negative_checker($totalminutes);
			// print_r($totalminutes."---");
				//overtime
			if($totalminutes > $workingmins){
				$overtime = $totalminutes - $workingmins;
			}else{
				$overtime = 0;
			}
			$overtime = negative_checker($overtime);
				//insert
			//will check if the overtime is approved, if not. no OT
			$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
			if($check_ot != null){
				$ot_temp = $overtime;
				$overtime = check_overtime($ot_temp,$check_ot->minutes_of_overtime);
			}else{
				$overtime = 0;
				}

				return $overtime;
			//will check if the overtime is approved, if not. no OT
		}

		else{
		}
				//else with
		break;
		}
		}
		else{
		$noworksched = 0;
			return $noworksched;
				}
			}

		if($purpose == "others"){
			$get_remaining_workorders = $trs->Timerecordsummary_model->get_all_workorders_others($employee_idno,$date_created);
			$get_remaining_workorders_result = $get_remaining_workorders->result();
			foreach($get_remaining_workorders_result as $g_r_w_r){
			$st = $g_r_w_r->start_time;
			$et = $g_r_w_r->end_time;
			$start_time = convert_to_minutes($st);
			$end_time = convert_to_minutes($et);
			$employee_idno = $g_r_w_r->employee_id;
			$date_timelog = $g_r_w_r->date;
			$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
			$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$overtime = 0;
			//start flexi computation
			$totalminutes = $end_time - $start_time;
			$totalminutes = $totalminutes - $breakmins;
			//overtime
			if($totalminutes > $workingmins){
				$overtime = $totalminutes - $workingmins;
			}else{
				$overtime = 0;
			}
			//totalminutes
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
				//-----------------------Overtime Checker-----------------------
				//--------------------------------------------------------------
			$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
			if($check_ot != null){
				$ot_temp = $overtime;
				$overtime = check_overtime($ot_temp,$check_ot->minutes_of_overtime);
			}else{
				$overtime = 0;
				}
				//equivalent to Day-off Workorder
				if($totalminutes > 0){
					return $overtime;
				}

			}
		}
}
function compute_undertime($employee_idno,$date_created,$purpose){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	if($purpose == "time_recording"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog($employee_idno,$date_created);
	}
	else if($purpose == "others"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}else{
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}
	$datesresult = $getdates->result();
	$datelength = $getdates->num_rows();
	//this will get all the time in and out of all the timelog that are not on trs
	//this will get all data from workorder that is not present in trs
	$alltimelog = array();
	$rescounter = 0;
	foreach($datesresult as $dr){
		$rescounter++;
		$employee_idno = $dr->employee_idno;
		$date_timelog = $dr->date;
		$date_timelog_day = date('w', strtotime($date_timelog));
		// $result    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date_timelog)));
		$workschedcounter = $trs->Timerecordsummary_model->countworkorder($employee_idno)->num_rows();
		if($workschedcounter > 0){
		//first time in
		$fti_data = $trs->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
		//last time out
		$lto_data = $trs->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
		//last time in
		$lti_data = $trs->Timerecordsummary_model->get_last_time_in($employee_idno,$date_timelog)->row();
		//first time out
		$fto_data = $trs->Timerecordsummary_model->get_first_time_out($employee_idno,$date_timelog)->row();
		$fti = $fti_data->time_in;
		$lto = $lto_data->time_out;
		$lti = $lti_data->time_in;
		$fto = $fto_data->time_out;
		//timelogcounterperday
		$multipletimelogcounter = $trs->Timerecordsummary_model->count_timelog($employee_idno,$date_timelog)->num_rows();
		//this will determine if schedule is fixed or flexi
		$st = $trs->Timerecordsummary_model->getschedtype($employee_idno)->row();
		$schedtype = $st->sched_type;
		//this will get the schedule of the employee on work schedule
		$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
		// $worksched_day = $worksched->work_sched;
		// print_r(array($lto,$fti));
		$timelogdata = array($employee_idno,$date_timelog,$fti,$lto);
		$alltimelog[] = $timelogdata;
		$get_sched_day = json_decode($worksched->work_sched);
		// print_r($get_sched_day->mon[]);
		//will get the schedule of employee - days
			//constant values
		$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
		$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$late = 0;
			$undertime = 0;
			$overtime = 0;
			$absent = 0;
			$manhours = 0;
		$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
		if($check_ot != null){
			$ot_mins = $check_ot->minutes_of_overtime;
		}else{
			$ot_mins = 0;
		}
		//----------------------Will determine the workschedule of the database-------------------------------
		//----------------------Will also provide sched and convert to minutes--------------------------------
		$get_worksched = get_worksched($employee_idno,$date_timelog_day);
		$sched_availability = $get_worksched['sched_availability'];
		$sched_ti_raw = $get_worksched['sched_ti_raw'];
		$sched_to_raw = $get_worksched['sched_to_raw'];
		$sched_ti = $get_worksched['sched_ti'];
		$sched_to = $get_worksched['sched_to'];
		$sched_break_start = $get_worksched['sched_break_start'];
		$sched_break_end = $get_worksched['sched_break_end'];
		$sched_break_mins = $get_worksched['sched_break_mins'];
		//convert actual timein/out to  minutes
		//first time in:
		$actual_timein = convert_to_minutes($fti);
		//last time out:
		$actual_timeout = convert_to_minutes($lto);
		//first time out
		$first_timeout = convert_to_minutes($fto);
		//last time in
		$last_timein = convert_to_minutes($lti);
		//--------------------Will check first the schedule availability of employee-----------------
		switch($sched_availability){
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work schedule-------------------
		case "with_worksched": //check if with work schedule
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
			//checks if employee first time in > $sched_break_end
			if($schedtype == 'fix'){
				//checks if employee first time in > $end schedule
				$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
				if($check_endshift_timelog == "too_late"){
					continue;
				}
				//checks if employee last time in < $sched_break_start
				$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
				if($check_startshift_timelog == "too_early"){
					continue;
				}
			}
			//checks if employee first time in > first_timeout
			if($actual_timein > $actual_timeout){
				continue;
			}
			//if  multiple timelog
				if($multipletimelogcounter > 1){
				$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
				$total_minutes_base= 0;
				//this will get exceeding hours
				$fix_workmins_controller = 0;
				// $lunch_deduction_base = 0;
				$lunch_deduction = 0;
				$alltimeout = array();
				$alltimein = array();
				//for checking of overlaps of ti and to
				$temp_out = 0;
				//checking of exceeding unfiled OT
				$ot_count = 0;
				//this will looop and count all timelogs of employees
				foreach($get_multiple_timelog as $gmt){
					$mt_timeout = $gmt->time_out;
					$mt_timein = $gmt->time_in;
					//converts mo minutes
					//time out
					$mtto_mins = convert_to_minutes($mt_timeout);
					//time in
					$mtti_mins = convert_to_minutes($mt_timein);
					//for checking of overlaps of ti and to
					if($mtti_mins < $temp_out){
						$mtti_mins = $temp_out;
					}
					$temp_out = $mtto_mins;
					//-----------------------------------
					$at_val = array(
						'in' => $mtti_mins,
						'out' => $mtto_mins
					);
					array_push($alltimeout,$mtto_mins);
					array_push($alltimein,$mtti_mins);
					//will check fix schedule if exceeded on sched time out
					if($schedtype == 'fix'){
						if($mtto_mins > $sched_to)
						{
							if($mtti_mins < $sched_to){
								$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
							}else if($mtti_mins >= $sched_to){
								if($check_ot != null){
									$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $mtti_mins);
								}else{
									//no OT filed
									$ot_count = $ot_count + ($mtto_mins - $mtti_mins);
									$fix_workmins_controller = $fix_workmins_controller + 0;
								}

							}
						}
					}
					$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
					// print_r($lunch_deduction_base);
				}
				$total_minutes_base = check_total_minutes_base($ot_mins,$ot_count,$total_minutes_base);
				$totalminutes = $total_minutes_base;
				$tm = $totalminutes;
				if($schedtype == 'fix'){
					//end is start break. start is end break
					$gc_start_break = getClosest($sched_break_start,$alltimeout);
					$key = array_search($gc_start_break,$alltimeout);
					$start_break_partner = $alltimein[$key];
					$gc_end_break = getClosest($sched_break_end,$alltimein);
					//returns the break greater thank the start break and prevents returning two equal results
					// if($gc_end_break < $gc_start_break){
					// 	$less_than_to_mins = array_search($gc_end_break,$alltimein);
					// 	unset($alltimein[$less_than_to_mins]);
					// }
					// $gc_end_break = getClosest($sched_break_end,$alltimein);
					$key2 = array_search($gc_end_break,$alltimein);
					$end_break_partner = $alltimeout[$key2];

					$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
							//subtract fix lunch break
					$totalminutes = $totalminutes - $lunch_deduction['fix_lunch_break'] - $fix_workmins_controller;
					$workingmins = fix_schedule_hours($sched_to,$sched_ti,$sched_break_start,$sched_break_end);
					$workingmins = negative_checker($workingmins);
					$undertime = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['undertime'];
					$undertime = negative_checker($undertime);
					}
					else if($schedtype == 'flexi'){
						//-------COMPUTATION FOR FLEXI-------
						$overbreak = 0;
						$totalminutes = $totalminutes - $breakmins;
						$totalminutes = negative_checker($totalminutes);
						//undertime
						if($totalminutes < $workingmins){
							$undertime = $workingmins - $totalminutes;
						}else{
							$undertime = 0;
						}
						$undertime = negative_checker($undertime);
						//totalminutes
					}
					else{
						$undertime = 0;
					}
				}
				//----------------------------single timelog with workorder-----------------------------
				//--------------------------------------------------------------------------------------
				else{
				//will check if time in = time out
				if($actual_timeout == $actual_timein){
					continue;
				}
				//will void time in and out greater than sched_to
				if($sched_to < $actual_timein){
					continue;
				}
				if($schedtype == "fix"){
					//work order values
					$overtime = 0;
					$undertime = 0;
					$late = 0;
					$overbreak = 0;
					//checks if actual_timein is too early
					if($sched_ti > $actual_timein){
						$actual_timein = $sched_ti;
					}
					//checks if actual_timein is too early
					if($sched_ti > $actual_timein){
						$actual_timein = $sched_ti;
					}
					$totalminutes = ($actual_timeout - $actual_timein)  - $sched_break_mins;
					$totalminutes = gethalfday_mins_single($actual_timeout,$actual_timein,$sched_break_start,$sched_break_end,$sched_break_mins,$totalminutes,$sched_ti);
					$totalminutes = negative_checker($totalminutes);
					//late
					if($sched_ti < $actual_timein){
						$late = $actual_timein - $sched_ti;
					}
					$late = negative_checker($late);
					//undertime
					if($late > 0 ){ //late dumating tapos maaga umuwi
						if($actual_timeout < $sched_to){
							$undertime = $workingmins - ($totalminutes + $late);
						}
					}else{
						// if($sched_ti <= $actual_timein){
							if($actual_timeout <= $sched_to){
								$undertime = $workingmins - $totalminutes;
							}else{
								$undertime = $actual_timein - $sched_ti;
							}
						// }
					}
					// print_r($undertime);
					$undertime = negative_checker($undertime);
					//overtime
				}
				else if($schedtype	== "flexi"){
					$totalminutes = ($actual_timeout - $actual_timein)  - $breakmins;
					$totalminutes = negative_checker($totalminutes);
					//overtime
					//undertime
					if($totalminutes < $workingmins){
						$undertime = $workingmins - $totalminutes;
					}else{
						$undertime = 0;
					}
					$undertime = negative_checker($undertime);
				}
				else{
					$undertime = 0;
					}
				}

				return $undertime;
		break;
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work no schedule-------------------
		case "without_worksched";
		//if with work order
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
		//select all dates on workorder and convert it to days. then check it to time record summary if it exist
		//override the time in, time out that is in timerecordsummary_trial that is not in workorder
		if($getworkorder != null){
			$overbreak = 0;
			$fti = $getworkorder->start_time;
			$lto = $getworkorder->end_time;
			$actual_timein = convert_to_minutes($fti);
			//last time ou:
			// $lto_explode = explode(':', $lto);
			$actual_timeout = convert_to_minutes($lto);
			$totalminutes = ($actual_timeout - $actual_timein) - $breakmins;
			$totalminutes = negative_checker($totalminutes);
			// print_r($totalminutes."---");
				//overtime
			if($totalminutes > $workingmins){
				$overtime = $totalminutes - $workingmins;
			}else{
				$overtime = 0;
			}
			$overtime = negative_checker($overtime);
			//undertime
			if($totalminutes < $workingmins){
				$undertime = $workingmins - $totalminutes;
			}else{
					$undertime = 0;
			}
			$undertime = negative_checker($undertime);
			return $undertime;
			//will check if the overtime is approved, if not. no OT
		}

		else{
		}
				//else with
		break;
		}
		//convert hours to minutes
		//insert to database
		//timein,timeout,manhrs,late,ot,ut,absent,totalminutes
		// }
		}
		else{
		$noworksched = 0;
		return $noworksched;
				}
			}

		if($purpose == "others"){
			$get_remaining_workorders = $trs->Timerecordsummary_model->get_all_workorders_others($employee_idno,$date_created);
			$get_remaining_workorders_result = $get_remaining_workorders->result();
			foreach($get_remaining_workorders_result as $g_r_w_r){
			$st = $g_r_w_r->start_time;
			$et = $g_r_w_r->end_time;
			$start_time = convert_to_minutes($st);
			$end_time = convert_to_minutes($et);
			$employee_idno = $g_r_w_r->employee_id;
			$date_timelog = $g_r_w_r->date;
			$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
			$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$undertime = 0;
			//start flexi computation
			$totalminutes = $end_time - $start_time;
			$totalminutes = $totalminutes - $breakmins;
			// undertime
			if($totalminutes < $workingmins){
				$undertime = $workingmins - $totalminutes;
			}else{
				$undertime = 0;
			}
			//totalminutes
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
			$remarks = 3;
			if($totalminutes > 0){
				return $undertime;
			}

			}
		}
}
function compute_overbreak($employee_idno,$date_created,$purpose){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	if($purpose == "time_recording"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog($employee_idno,$date_created);
	}
	else if($purpose == "others"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}else{
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}
	$datesresult = $getdates->result();
	$datelength = $getdates->num_rows();
	//this will get all the time in and out of all the timelog that are not on trs
	//this will get all data from workorder that is not present in trs
	$alltimelog = array();
	$rescounter = 0;
	foreach($datesresult as $dr){
		$rescounter++;
		$employee_idno = $dr->employee_idno;
		$date_timelog = $dr->date;
		$date_timelog_day = date('w', strtotime($date_timelog));
		// $result    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date_timelog)));
		$workschedcounter = $trs->Timerecordsummary_model->countworkorder($employee_idno)->num_rows();
		if($workschedcounter > 0){
		//first time in
		$fti_data = $trs->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
		//last time out
		$lto_data = $trs->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
		//last time in
		$lti_data = $trs->Timerecordsummary_model->get_last_time_in($employee_idno,$date_timelog)->row();
		//first time out
		$fto_data = $trs->Timerecordsummary_model->get_first_time_out($employee_idno,$date_timelog)->row();
		$fti = $fti_data->time_in;
		$lto = $lto_data->time_out;
		$lti = $lti_data->time_in;
		$fto = $fto_data->time_out;
		//timelogcounterperday
		$multipletimelogcounter = $trs->Timerecordsummary_model->count_timelog($employee_idno,$date_timelog)->num_rows();
		//this will determine if schedule is fixed or flexi
		$st = $trs->Timerecordsummary_model->getschedtype($employee_idno)->row();
		$schedtype = $st->sched_type;
		//this will get the schedule of the employee on work schedule
		$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
		// $worksched_day = $worksched->work_sched;
		// print_r(array($lto,$fti));
		$timelogdata = array($employee_idno,$date_timelog,$fti,$lto);
		$alltimelog[] = $timelogdata;
		$get_sched_day = json_decode($worksched->work_sched);
		// print_r($get_sched_day->mon[]);
		//will get the schedule of employee - days
			//constant values
		$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
		$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$late = 0;
			$undertime = 0;
			$overtime = 0;
			$absent = 0;
			$manhours = 0;
		$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
		if($check_ot != null){
			$ot_mins = $check_ot->minutes_of_overtime;
		}else{
			$ot_mins = 0;
		}
		//----------------------Will determine the workschedule of the database-------------------------------
		//----------------------Will also provide sched and convert to minutes--------------------------------
		$get_worksched = get_worksched($employee_idno,$date_timelog_day);
		$sched_availability = $get_worksched['sched_availability'];
		$sched_ti_raw = $get_worksched['sched_ti_raw'];
		$sched_to_raw = $get_worksched['sched_to_raw'];
		$sched_ti = $get_worksched['sched_ti'];
		$sched_to = $get_worksched['sched_to'];
		$sched_break_start = $get_worksched['sched_break_start'];
		$sched_break_end = $get_worksched['sched_break_end'];
		$sched_break_mins = $get_worksched['sched_break_mins'];
		//convert actual timein/out to  minutes
		//first time in:
		$actual_timein = convert_to_minutes($fti);
		//last time out:
		$actual_timeout = convert_to_minutes($lto);
		//first time out
		$first_timeout = convert_to_minutes($fto);
		//last time in
		$last_timein = convert_to_minutes($lti);
		//--------------------Will check first the schedule availability of employee-----------------
		switch($sched_availability){
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work schedule-------------------
		case "with_worksched": //check if with work schedule
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
			//checks if employee first time in > $sched_break_end
			if($schedtype == 'fix'){
				//checks if employee first time in > $end schedule
				$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
				if($check_endshift_timelog == "too_late"){
					continue;
				}
				//checks if employee last time in < $sched_break_start
				$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
				if($check_startshift_timelog == "too_early"){
					continue;
				}
			}
			//checks if employee first time in > first_timeout
			if($actual_timein > $actual_timeout){
				continue;
			}
			//if  multiple timelog
				if($multipletimelogcounter > 1){
				$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
				$total_minutes_base= 0;
				//this will get exceeding hours
				$fix_workmins_controller = 0;
				// $lunch_deduction_base = 0;
				$lunch_deduction = 0;
				$alltimeout = array();
				$alltimein = array();
				//for checking of overlaps of ti and to
				$temp_out = 0;
				//checking of exceeding unfiled OT
				$ot_count = 0;
				//this will looop and count all timelogs of employees
				foreach($get_multiple_timelog as $gmt){
					$mt_timeout = $gmt->time_out;
					$mt_timein = $gmt->time_in;
					//converts mo minutes
					//time out
					$mtto_mins = convert_to_minutes($mt_timeout);
					//time in
					$mtti_mins = convert_to_minutes($mt_timein);
					//for checking of overlaps of ti and to
					if($mtti_mins < $temp_out){
						$mtti_mins = $temp_out;
					}
					$temp_out = $mtto_mins;
					//-----------------------------------
					$at_val = array(
						'in' => $mtti_mins,
						'out' => $mtto_mins
					);
					array_push($alltimeout,$mtto_mins);
					array_push($alltimein,$mtti_mins);
					//will check fix schedule if exceeded on sched time out
					if($schedtype == 'fix'){
						if($mtto_mins > $sched_to)
						{
							if($mtti_mins < $sched_to){
								$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
							}else if($mtti_mins >= $sched_to){
								if($check_ot != null){
									$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $mtti_mins);
								}else{
									//no OT filed
									$ot_count = $ot_count + ($mtto_mins - $mtti_mins);
									$fix_workmins_controller = $fix_workmins_controller + 0;
								}

							}
						}
					}
					$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
					// print_r($lunch_deduction_base);
				}
				$total_minutes_base = check_total_minutes_base($ot_mins,$ot_count,$total_minutes_base);
				$totalminutes = $total_minutes_base;
				$tm = $totalminutes;
				if($schedtype == 'fix'){
					//end is start break. start is end break
					$gc_start_break = getClosest($sched_break_start,$alltimeout);
					$key = array_search($gc_start_break,$alltimeout);
					$start_break_partner = $alltimein[$key];
					$gc_end_break = getClosest($sched_break_end,$alltimein);
					//returns the break greater thank the start break and prevents returning two equal results
					// if($gc_end_break < $gc_start_break){
					// 	$less_than_to_mins = array_search($gc_end_break,$alltimein);
					// 	unset($alltimein[$less_than_to_mins]);
					// }
					// $gc_end_break = getClosest($sched_break_end,$alltimein);
					$key2 = array_search($gc_end_break,$alltimein);
					$end_break_partner = $alltimeout[$key2];

					$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
							//subtract fix lunch break
					$overbreak = $lunch_deduction['lunch_deduction'];
					$overbreak = negative_checker($overbreak);
					}
					else if($schedtype == 'flexi'){
						//-------COMPUTATION FOR FLEXI-------
						$overbreak = 0;
					}
					else{
						$overbreak = 0;
					}
				}
				//----------------------------single timelog with workorder-----------------------------
				//--------------------------------------------------------------------------------------
				else{
				//will check if time in = time out
				if($actual_timeout == $actual_timein){
					continue;
				}
				//will void time in and out greater than sched_to
				if($sched_to < $actual_timein){
					continue;
				}
				if($schedtype == "fix"){
					//work order values
					$overbreak = 0;
				}
				else if($schedtype	== "flexi"){
					$overbreak = 0;
				}
				else{
					$overbreak = 0;
					}
				}
			return $overbreak;
		break;
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work no schedule-------------------
		case "without_worksched";
		//if with work order
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
		//select all dates on workorder and convert it to days. then check it to time record summary if it exist
		//override the time in, time out that is in timerecordsummary_trial that is not in workorder
		if($getworkorder != null){
			$overbreak = 0;


			return $overbreak;
			//will check if the overtime is approved, if not. no OT
		}

		else{
		}
				//else with
		break;
		}
		//convert hours to minutes
		//insert to database
		//timein,timeout,manhrs,late,ot,ut,absent,totalminutes
		// }
		}
		else{
		$noworksched = 0;
		return $noworksched;
				}
			}


		if($purpose == "others"){
			$get_remaining_workorders = $trs->Timerecordsummary_model->get_all_workorders_others($employee_idno,$date_created);
			$get_remaining_workorders_result = $get_remaining_workorders->result();
			foreach($get_remaining_workorders_result as $g_r_w_r){
			$st = $g_r_w_r->start_time;
			$et = $g_r_w_r->end_time;
			$start_time = convert_to_minutes($st);
			$end_time = convert_to_minutes($et);
			$employee_idno = $g_r_w_r->employee_id;
			$date_timelog = $g_r_w_r->date;
			$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
			$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$overbreak = 0;
			//start flexi computation
			$totalminutes = $end_time - $start_time;
			$totalminutes = $totalminutes - $breakmins;

			//totalminutes
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
				if($totalminutes > 0){
					return $overbreak;
				}

			}
		}
}
function compute_manhours($employee_idno,$date_created,$purpose){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	if($purpose == "time_recording"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog($employee_idno,$date_created);
	}
	else if($purpose == "others"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}else{
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}
	$datesresult = $getdates->result();
	$datelength = $getdates->num_rows();
	//this will get all the time in and out of all the timelog that are not on trs
	//this will get all data from workorder that is not present in trs
	$alltimelog = array();
	$rescounter = 0;
	foreach($datesresult as $dr){
		$rescounter++;
		$employee_idno = $dr->employee_idno;
		$date_timelog = $dr->date;
		$date_timelog_day = date('w', strtotime($date_timelog));
		// $result    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date_timelog)));
		$workschedcounter = $trs->Timerecordsummary_model->countworkorder($employee_idno)->num_rows();
		if($workschedcounter > 0){
		//first time in
		$fti_data = $trs->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
		//last time out
		$lto_data = $trs->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
		//last time in
		$lti_data = $trs->Timerecordsummary_model->get_last_time_in($employee_idno,$date_timelog)->row();
		//first time out
		$fto_data = $trs->Timerecordsummary_model->get_first_time_out($employee_idno,$date_timelog)->row();
		$fti = $fti_data->time_in;
		$lto = $lto_data->time_out;
		$lti = $lti_data->time_in;
		$fto = $fto_data->time_out;
		//timelogcounterperday
		$multipletimelogcounter = $trs->Timerecordsummary_model->count_timelog($employee_idno,$date_timelog)->num_rows();
		//this will determine if schedule is fixed or flexi
		$st = $trs->Timerecordsummary_model->getschedtype($employee_idno)->row();
		$schedtype = $st->sched_type;
		//this will get the schedule of the employee on work schedule
		$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
		// $worksched_day = $worksched->work_sched;
		// print_r(array($lto,$fti));
		$timelogdata = array($employee_idno,$date_timelog,$fti,$lto);
		$alltimelog[] = $timelogdata;
		$get_sched_day = json_decode($worksched->work_sched);
		// print_r($get_sched_day->mon[]);
		//will get the schedule of employee - days
			//constant values
		$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
		$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$late = 0;
			$undertime = 0;
			$overtime = 0;
			$absent = 0;
			$manhours = 0;
		$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
		if($check_ot != null){
			$ot_mins = $check_ot->minutes_of_overtime;
		}else{
			$ot_mins = 0;
		}
		//----------------------Will determine the workschedule of the database-------------------------------
		//----------------------Will also provide sched and convert to minutes--------------------------------
		$get_worksched = get_worksched($employee_idno,$date_timelog_day);
		$sched_availability = $get_worksched['sched_availability'];
		$sched_ti_raw = $get_worksched['sched_ti_raw'];
		$sched_to_raw = $get_worksched['sched_to_raw'];
		$sched_ti = $get_worksched['sched_ti'];
		$sched_to = $get_worksched['sched_to'];
		$sched_break_start = $get_worksched['sched_break_start'];
		$sched_break_end = $get_worksched['sched_break_end'];
		$sched_break_mins = $get_worksched['sched_break_mins'];
		//convert actual timein/out to  minutes
		//first time in:
		$actual_timein = convert_to_minutes($fti);
		//last time out:
		$actual_timeout = convert_to_minutes($lto);
		//first time out
		$first_timeout = convert_to_minutes($fto);
		//last time in
		$last_timein = convert_to_minutes($lti);
		//--------------------Will check first the schedule availability of employee-----------------
		switch($sched_availability){
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work schedule-------------------
		case "with_worksched": //check if with work schedule
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
			//checks if employee first time in > $sched_break_end
			if($schedtype == 'fix'){
				//checks if employee first time in > $end schedule
				$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
				if($check_endshift_timelog == "too_late"){
					continue;
				}
				//checks if employee last time in < $sched_break_start
				$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
				if($check_startshift_timelog == "too_early"){
					continue;
				}
			}
			//checks if employee first time in > first_timeout
			if($actual_timein > $actual_timeout){
				continue;
			}
			//if  multiple timelog
				if($multipletimelogcounter > 1){
				$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
				$total_minutes_base= 0;
				//this will get exceeding hours
				$fix_workmins_controller = 0;
				// $lunch_deduction_base = 0;
				$lunch_deduction = 0;
				$alltimeout = array();
				$alltimein = array();
				//for checking of overlaps of ti and to
				$temp_out = 0;
				//checking of exceeding unfiled OT
				$ot_count = 0;
				//this will looop and count all timelogs of employees
				foreach($get_multiple_timelog as $gmt){
					$mt_timeout = $gmt->time_out;
					$mt_timein = $gmt->time_in;
					//converts mo minutes
					//time out
					$mtto_mins = convert_to_minutes($mt_timeout);
					//time in
					$mtti_mins = convert_to_minutes($mt_timein);

					//for checking of overlaps of ti and to
					if($mtti_mins < $temp_out){
						$mtti_mins = $temp_out;
					}
					$temp_out = $mtto_mins;
					//-----------------------------------
					$at_val = array(
						'in' => $mtti_mins,
						'out' => $mtto_mins
					);
					array_push($alltimeout,$mtto_mins);
					array_push($alltimein,$mtti_mins);
					//will check fix schedule if exceeded on sched time out
					if($schedtype == 'fix'){
						if($mtto_mins > $sched_to)
						{
							if($mtti_mins < $sched_to){
								$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
							}else if($mtti_mins >= $sched_to){
								if($check_ot != null){
									$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $mtti_mins);
								}else{
									//no OT filed
									$ot_count = $ot_count + ($mtto_mins - $mtti_mins);
									$fix_workmins_controller = $fix_workmins_controller + 0;
								}

							}
						}
					}
					$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
					// print_r($lunch_deduction_base);
				}
				$total_minutes_base = check_total_minutes_base($ot_mins,$ot_count,$total_minutes_base);
				$totalminutes = $total_minutes_base;
				$tm = $totalminutes;
				if($schedtype == 'fix'){
					//end is start break. start is end break
					$gc_start_break = getClosest($sched_break_start,$alltimeout);
					$key = array_search($gc_start_break,$alltimeout);
					$start_break_partner = $alltimein[$key];
					$gc_end_break = getClosest($sched_break_end,$alltimein);
					//returns the break greater thank the start break and prevents returning two equal results
					// if($gc_end_break < $gc_start_break){
					// 	$less_than_to_mins = array_search($gc_end_break,$alltimein);
					// 	unset($alltimein[$less_than_to_mins]);
					// }
					// $gc_end_break = getClosest($sched_break_end,$alltimein);
					$key2 = array_search($gc_end_break,$alltimein);
					$end_break_partner = $alltimeout[$key2];

					$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
							//subtract fix lunch break
					$totalminutes = $totalminutes - $lunch_deduction['fix_lunch_break'] - $fix_workmins_controller;
					$workingmins = fix_schedule_hours($sched_to,$sched_ti,$sched_break_start,$sched_break_end);
					$workingmins = negative_checker($workingmins);
					$manhours =  gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['manhours'];
					$manhours = negative_checker($manhours);
					}
					else if($schedtype == 'flexi'){
						//-------COMPUTATION FOR FLEXI-------
						$overbreak = 0;
						$totalminutes = $totalminutes - $breakmins;
						$totalminutes = negative_checker($totalminutes);
						//totalminutes
						if($totalminutes > $workingmins){
							$totalminutes = $workingmins;
						}
						$manhours = round(($totalminutes / 60),2);
					}
					else{
						$manhours = 0;
					}
				}
				//----------------------------single timelog with workorder-----------------------------
				//--------------------------------------------------------------------------------------
				else{
				//will check if time in = time out
				if($actual_timeout == $actual_timein){
					continue;
				}
				//will void time in and out greater than sched_to
				if($sched_to < $actual_timein){
					continue;
				}
				if($schedtype == "fix"){
					//work order values
					$overtime = 0;
					$undertime = 0;
					$late = 0;
					$overbreak = 0;
					//checks if actual_timein is too early
					if($sched_ti > $actual_timein){
						$actual_timein = $sched_ti;
					}
					$totalminutes = ($actual_timeout - $actual_timein)  - $sched_break_mins;
					$totalminutes = gethalfday_mins_single($actual_timeout,$actual_timein,$sched_break_start,$sched_break_end,$sched_break_mins,$totalminutes,$sched_ti);
					$totalminutes = negative_checker($totalminutes);
					//late
					if($sched_ti < $actual_timein){
						$late = $actual_timein - $sched_ti;
					}
					$late = negative_checker($late);
					//undertime
					if($late > 0 ){ //late dumating tapos maaga umuwi
						if($actual_timeout < $sched_to){
							$undertime = $workingmins - ($totalminutes + $late);
						}
					}else{
						if($sched_ti <= $actual_timein){
							if($actual_timeout <= $sched_to){
								$undertime = $workingmins - $totalminutes;
							}else{
								$undertime = $actual_timein - $sched_ti;
							}
						}
					}
					$undertime = negative_checker($undertime);
					$fix_sched_fixer = $totalminutes + $undertime + $late;
					if($fix_sched_fixer >= $workingmins){
						$totalminutes = $workingmins - ($undertime + $late);
					}
					if($totalminutes >= $workingmins){
						$totalminutes = $workingmins;
					}
					$manhours = round(($totalminutes / 60),2);
				}
				else if($schedtype	== "flexi"){
					$overbreak = 0;
					$totalminutes = ($actual_timeout - $actual_timein)  - $breakmins;
					$totalminutes = negative_checker($totalminutes);
					//manhours
					if($totalminutes > $workingmins){
						$totalminutes = $workingmins;
					}
						$manhours = round(($totalminutes / 60),2);
				}
				else{
					$manhours = 0;
					}
				}
			return $manhours;
		break;
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work no schedule-------------------
		case "without_worksched";
		//if with work order
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
		//select all dates on workorder and convert it to days. then check it to time record summary if it exist
		//override the time in, time out that is in timerecordsummary_trial that is not in workorder
		if($getworkorder != null){
			$overbreak = 0;
			$fti = $getworkorder->start_time;
			$lto = $getworkorder->end_time;
			$actual_timein = convert_to_minutes($fti);
			//last time ou:
			// $lto_explode = explode(':', $lto);
			$actual_timeout = convert_to_minutes($lto);
			$totalminutes = ($actual_timeout - $actual_timein) - $breakmins;
			$totalminutes = negative_checker($totalminutes);
			// print_r($totalminutes."---");
				//overtime
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
			//manhours
			if($totalminutes != 0){
				$totalhours = $totalminutes / 60;
				$manhours = round($totalhours,2);
			}else{
				$manhours = 0;
			}
				//insert
			//will check if the overtime is approved, if not. no OT
			return $manhours;
			//will check if the overtime is approved, if not. no OT
		}

		else{
		}
				//else with
		break;
		}
		//convert hours to minutes
		//insert to database
		//timein,timeout,manhrs,late,ot,ut,absent,totalminutes
		// }
		}
		else{
		$noworksched = 0;
		return $noworksched;
				}
			}


		if($purpose == "others"){
			$get_remaining_workorders = $trs->Timerecordsummary_model->get_all_workorders_others($employee_idno,$date_created);
			$get_remaining_workorders_result = $get_remaining_workorders->result();
			foreach($get_remaining_workorders_result as $g_r_w_r){
			$st = $g_r_w_r->start_time;
			$et = $g_r_w_r->end_time;
			$start_time = convert_to_minutes($st);
			$end_time = convert_to_minutes($et);
			$employee_idno = $g_r_w_r->employee_id;
			$date_timelog = $g_r_w_r->date;
			$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
			$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$manhours = 0;
			//start flexi computation
			$totalminutes = $end_time - $start_time;
			$totalminutes = $totalminutes - $breakmins;
			//totalminutes
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
			$manhours = round(($totalminutes / 60),2);
				//-----------------------Overtime Checker-----------------------
				//--------------------------------------------------------------
						//equivalent to Day-off Workorder
				$remarks = 3;
				if($totalminutes > 0){
					return $manhours;
				}

			}
		}
}
function compute_totalminutes($employee_idno,$date_created,$purpose){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	if($purpose == "time_recording"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog($employee_idno,$date_created);
	}
	else if($purpose == "others"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}else{
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}
	$datesresult = $getdates->result();
	$datelength = $getdates->num_rows();
	//this will get all the time in and out of all the timelog that are not on trs
	//this will get all data from workorder that is not present in trs
	$alltimelog = array();
	$rescounter = 0;
	foreach($datesresult as $dr){
		$rescounter++;
		$employee_idno = $dr->employee_idno;
		$date_timelog = $dr->date;
		$date_timelog_day = date('w', strtotime($date_timelog));
		// $result    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date_timelog)));
		$workschedcounter = $trs->Timerecordsummary_model->countworkorder($employee_idno)->num_rows();
		if($workschedcounter > 0){
		//first time in
		$fti_data = $trs->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
		//last time out
		$lto_data = $trs->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
		//last time in
		$lti_data = $trs->Timerecordsummary_model->get_last_time_in($employee_idno,$date_timelog)->row();
		//first time out
		$fto_data = $trs->Timerecordsummary_model->get_first_time_out($employee_idno,$date_timelog)->row();
		$fti = $fti_data->time_in;
		$lto = $lto_data->time_out;
		$lti = $lti_data->time_in;
		$fto = $fto_data->time_out;
		//timelogcounterperday
		$multipletimelogcounter = $trs->Timerecordsummary_model->count_timelog($employee_idno,$date_timelog)->num_rows();
		//this will determine if schedule is fixed or flexi
		$st = $trs->Timerecordsummary_model->getschedtype($employee_idno)->row();
		$schedtype = $st->sched_type;
		//this will get the schedule of the employee on work schedule
		$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
		// $worksched_day = $worksched->work_sched;
		// print_r(array($lto,$fti));
		$timelogdata = array($employee_idno,$date_timelog,$fti,$lto);
		$alltimelog[] = $timelogdata;
		$get_sched_day = json_decode($worksched->work_sched);
		// print_r($get_sched_day->mon[]);
		//will get the schedule of employee - days
			//constant values
		$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
		$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$late = 0;
			$undertime = 0;
			$overtime = 0;
			$absent = 0;
			$manhours = 0;
		$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
		if($check_ot != null){
			$ot_mins = $check_ot->minutes_of_overtime;
		}else{
			$ot_mins = 0;
		}
		//----------------------Will determine the workschedule of the database-------------------------------
		//----------------------Will also provide sched and convert to minutes--------------------------------
		$get_worksched = get_worksched($employee_idno,$date_timelog_day);
		$sched_availability = $get_worksched['sched_availability'];
		$sched_ti_raw = $get_worksched['sched_ti_raw'];
		$sched_to_raw = $get_worksched['sched_to_raw'];
		$sched_ti = $get_worksched['sched_ti'];
		$sched_to = $get_worksched['sched_to'];
		$sched_break_start = $get_worksched['sched_break_start'];
		$sched_break_end = $get_worksched['sched_break_end'];
		$sched_break_mins = $get_worksched['sched_break_mins'];
		//convert actual timein/out to  minutes
		//first time in:
		$actual_timein = convert_to_minutes($fti);
		//last time out:
		$actual_timeout = convert_to_minutes($lto);
		//first time out
		$first_timeout = convert_to_minutes($fto);
		//last time in
		$last_timein = convert_to_minutes($lti);
		//--------------------Will check first the schedule availability of employee-----------------
		switch($sched_availability){
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work schedule-------------------
		case "with_worksched": //check if with work schedule
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
			//checks if employee first time in > $sched_break_end
			if($schedtype == 'fix'){
				//checks if employee first time in > $end schedule
				$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
				if($check_endshift_timelog == "too_late"){
					continue;
				}
				//checks if employee last time in < $sched_break_start
				$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
				if($check_startshift_timelog == "too_early"){
					continue;
				}
			}
			//checks if employee first time in > first_timeout
			if($actual_timein > $actual_timeout){
				continue;
			}
			//if  multiple timelog
				if($multipletimelogcounter > 1){
				$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
				$total_minutes_base= 0;
				//this will get exceeding hours
				$fix_workmins_controller = 0;
				// $lunch_deduction_base = 0;
				$lunch_deduction = 0;
				$alltimeout = array();
				$alltimein = array();
				//for checking of overlaps of ti and to
				$temp_out = 0;
				//checking of exceeding unfiled OT
				$ot_count = 0;
				//this will looop and count all timelogs of employees
				foreach($get_multiple_timelog as $gmt){
					$mt_timeout = $gmt->time_out;
					$mt_timein = $gmt->time_in;
					//converts mo minutes
					//time out
					$mtto_mins = convert_to_minutes($mt_timeout);
					//time in
					$mtti_mins = convert_to_minutes($mt_timein);

					//for checking of overlaps of ti and to
					if($mtti_mins < $temp_out){
						$mtti_mins = $temp_out;
					}
					$temp_out = $mtto_mins;
					//-----------------------------------
					$at_val = array(
						'in' => $mtti_mins,
						'out' => $mtto_mins
					);
					array_push($alltimeout,$mtto_mins);
					array_push($alltimein,$mtti_mins);
					//will check fix schedule if exceeded on sched time out
					if($schedtype == 'fix'){
						if($mtto_mins > $sched_to)
						{
							if($mtti_mins < $sched_to){
								$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
							}else if($mtti_mins >= $sched_to){
								if($check_ot != null){
									$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $mtti_mins);
								}else{
									//no OT filed
									$ot_count = $ot_count + ($mtto_mins - $mtti_mins);
									$fix_workmins_controller = $fix_workmins_controller + 0;
								}

							}
						}
					}
					$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
					// print_r($lunch_deduction_base);
				}
				$total_minutes_base = check_total_minutes_base($ot_mins,$ot_count,$total_minutes_base);
				$totalminutes = $total_minutes_base;
				$tm = $totalminutes;
				if($schedtype == 'fix'){
					//end is start break. start is end break
					$gc_start_break = getClosest($sched_break_start,$alltimeout);
					$key = array_search($gc_start_break,$alltimeout);
					$start_break_partner = $alltimein[$key];
					$gc_end_break = getClosest($sched_break_end,$alltimein);
					//returns the break greater thank the start break and prevents returning two equal results
					// if($gc_end_break < $gc_start_break){
					// 	$less_than_to_mins = array_search($gc_end_break,$alltimein);
					// 	unset($alltimein[$less_than_to_mins]);
					// }
					// $gc_end_break = getClosest($sched_break_end,$alltimein);
					$key2 = array_search($gc_end_break,$alltimein);
					$end_break_partner = $alltimeout[$key2];

					$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
							//subtract fix lunch break
					$totalminutes = $totalminutes - $lunch_deduction['fix_lunch_break'] - $fix_workmins_controller;
					$workingmins = fix_schedule_hours($sched_to,$sched_ti,$sched_break_start,$sched_break_end);
					$workingmins = negative_checker($workingmins);
					$totalminutes = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['totalminutes'];
					$totalminutes = negative_checker($totalminutes);
					}
					else if($schedtype == 'flexi'){
						//-------COMPUTATION FOR FLEXI-------
						$overbreak = 0;
						$totalminutes = $totalminutes - $breakmins;
						$totalminutes = negative_checker($totalminutes);
						//totalminutes
						if($totalminutes > $workingmins){
							$totalminutes = $workingmins;
						}
					}
					else{
						$totalminutes = 0;
					}
				}
				//----------------------------single timelog with workorder-----------------------------
				//--------------------------------------------------------------------------------------
				else{
				//will check if time in = time out
				if($actual_timeout == $actual_timein){
					continue;
				}
				//will void time in and out greater than sched_to
				if($sched_to < $actual_timein){
					continue;
				}
				if($schedtype == "fix"){
					//work order values
					$overtime = 0;
					$undertime = 0;
					$late = 0;
					$overbreak = 0;
					//checks if actual_timein is too early
					if($sched_ti > $actual_timein){
						$actual_timein = $sched_ti;
					}
					$totalminutes = ($actual_timeout - $actual_timein)  - $sched_break_mins;
					$totalminutes = gethalfday_mins_single($actual_timeout,$actual_timein,$sched_break_start,$sched_break_end,$sched_break_mins,$totalminutes,$sched_ti);
					$totalminutes = negative_checker($totalminutes);

					//late
					if($sched_ti < $actual_timein){
						$late = $actual_timein - $sched_ti;
					}
					$late = negative_checker($late);
					//undertime
					if($late > 0 ){ //late dumating tapos maaga umuwi
						if($actual_timeout < $sched_to){
							$undertime = $workingmins - ($totalminutes + $late);
						}
					}else{
						if($sched_ti <= $actual_timein){
							if($actual_timeout <= $sched_to){
								$undertime = $workingmins - $totalminutes;
							}else{
								$undertime = $actual_timein - $sched_ti;
							}
						}
					}
					$undertime = negative_checker($undertime);
					$fix_sched_fixer = $totalminutes + $undertime + $late;
					if($fix_sched_fixer >= $workingmins){
						$totalminutes = $workingmins - ($undertime + $late);
					}
					if($totalminutes >= $workingmins){
						$totalminutes = $workingmins;
					}
				}
				else if($schedtype	== "flexi"){
					$overbreak = 0;
					$totalminutes = ($actual_timeout - $actual_timein)  - $breakmins;
					$totalminutes = negative_checker($totalminutes);
					//manhours
					if($totalminutes > $workingmins){
						$totalminutes = $workingmins;
					}
				}
				else{
					$totalminutes = 0;
					}
				}
				return $totalminutes;
		break;
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work no schedule-------------------
		case "without_worksched";
		//if with work order
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
		//select all dates on workorder and convert it to days. then check it to time record summary if it exist
		//override the time in, time out that is in timerecordsummary_trial that is not in workorder
		if($getworkorder != null){
			$fti = $getworkorder->start_time;
			$lto = $getworkorder->end_time;
			$actual_timein = convert_to_minutes($fti);
			//last time ou:
			// $lto_explode = explode(':', $lto);
			$actual_timeout = convert_to_minutes($lto);
			$totalminutes = ($actual_timeout - $actual_timein) - $breakmins;
			$totalminutes = negative_checker($totalminutes);
			//totalminutes
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
			//manhours
			if($totalminutes != 0){
				$totalhours = $totalminutes / 60;
				$manhours = round($totalhours,2);
			}else{
				$manhours = 0;
			}

			return $totalminutes;
			//will check if the overtime is approved, if not. no OT
		}

		else{
		}
				//else with
		break;
		}
		}
		else{
		$noworksched = 0;
		return $noworksched;
				}
			}
		if($purpose == "others"){
			$get_remaining_workorders = $trs->Timerecordsummary_model->get_all_workorders_others($employee_idno,$date_created);
			$get_remaining_workorders_result = $get_remaining_workorders->result();
			foreach($get_remaining_workorders_result as $g_r_w_r){
			$st = $g_r_w_r->start_time;
			$et = $g_r_w_r->end_time;
			$start_time = convert_to_minutes($st);
			$end_time = convert_to_minutes($et);
			$employee_idno = $g_r_w_r->employee_id;
			$date_timelog = $g_r_w_r->date;
			$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
			$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			//start flexi computation
			$totalminutes = $end_time - $start_time;
			$totalminutes = $totalminutes - $breakmins;
			//totalminutes
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
				if($totalminutes > 0){
					return $totalminutes;
				}

			}
		}
}
function get_remarks($employee_idno,$date_created,$purpose){
	// print_r(array($employee_idno,$date_created));
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	$check_absent = fetch_absent($employee_idno,$date_created,$purpose);
	$check_holidays = $trs->Timerecordsummary_model->check_holidays($date_created)->row();
	$check_leave = $trs->Timerecordsummary_model->get_leave($date_created,$employee_idno);

	if($check_absent == 0){
		$date_timelog_day = date('w', strtotime($date_created));
		$get_worksched = get_worksched($employee_idno,$date_timelog_day);
		$sched_availability = $get_worksched['sched_availability'];
		switch($sched_availability){
		case "with_worksched":
		if($check_holidays != null){
			//remarks 1 - Holiday
			$remarks = 1;
		}else if($check_leave->num_rows() > 0){
			$remarks = 4;
		}else{
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_created)->row();
			if($getworkorder != null){
				$remarks = 2;
			}else{
				$remarks = 0;
			}
		}
		break;

		case "without_worksched":
		if($check_holidays != null){
			//remarks 1 - Holiday
			$remarks = 1;
		}else{
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_created)->row();
			if($getworkorder != null){
				$remarks = 3;
			}else{
				$remarks = 0;
			}
		}
		break;
		}

	}else if($check_absent == 1){
		if($check_holidays != null){
			//remarks 1 - Holiday
			$remarks = 1;
		}else{
			$remarks = 0;
		}
	}
	return $remarks;
}
function compute_remaining_workorder($employee_idno,$date_created,$purpose){
//return if satisfied, else continue and return
}
// function fetch_absent($employee_idno,$date_created,$purpose){
// 	$trs = get_instance();
// 	$trs->load->model('time_record/Timerecordsummary_model');
// 	$totalminutes = compute_totalminutes($employee_idno,$date_created,$purpose);
// 	if($totalminutes == ""){
// 		//hindi pumasok
// 		//check if employee has timelog
// 		$get_first_timelog = $trs->Timerecordsummary_model->check_first_timelog($employee_idno)->row();

// 		if($get_first_timelog != null){
// 			if($get_first_timelog->date > $date_created){
// 				//wala pa syang pasok
// 				$absent = 0;
// 			}else{
// 				$date_timelog_day = date('w', strtotime($date_created));
// 				$check_ws = get_worksched($employee_idno,$date_timelog_day)['sched_availability'];
// 				if($check_ws == "with_worksched"){
// 					//absent

// 					$check_holiday = $this->Timerecordsummary_model->check_holidays($date_created)->row();
// 					if($check_holiday != null){
// 						//may holiday
// 						$absent = 0;
// 					}else{
// 						//absent na talaga
// 						$absent = 1;
// 					}
// 				}else if($check_ws == "without_worksched"){
// 					//day off
// 					$absent = 0;
// 				}else{
// 					$absent = 0;
// 				}
// 			}
// 		}else{
// 			//wala pang timelog
// 			$absent = 0;
// 		}
// 	}
// 	else{
// 		//pumasok
// 		$absent = 0;
// 	}
// 	return $absent;
// }
function fetch_absent($employee_idno,$date_created,$purpose){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	$purpose = "";

	$check_absent = $trs->Timerecordsummary_model->check_absent_record_v2($employee_idno,$date_created);

	return $check_absent;
}
//for checking of total minutes on model to avoid repeating compute_totalminutes. same as totalminutes
function check_minutes($employee_idno,$date_created,$purpose){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	if($purpose == "time_recording"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog($employee_idno,$date_created);
	}
	else if($purpose == "others"){
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}else{
		$getdates = $trs->Timerecordsummary_model->get_all_dates_employees_timelog_others($employee_idno,$date_created);
	}
	$datesresult = $getdates->result();
	$datelength = $getdates->num_rows();
	//this will get all the time in and out of all the timelog that are not on trs
	//this will get all data from workorder that is not present in trs
	$alltimelog = array();
	$rescounter = 0;
	foreach($datesresult as $dr){
		$rescounter++;
		$employee_idno = $dr->employee_idno;
		$date_timelog = $dr->date;
		$date_timelog_day = date('w', strtotime($date_timelog));
		// $result    = date('Y-m-d', strtotime(($day - $dayofweek).' day', strtotime($date_timelog)));
		$workschedcounter = $trs->Timerecordsummary_model->countworkorder($employee_idno)->num_rows();
		if($workschedcounter > 0){
		//first time in
		$fti_data = $trs->Timerecordsummary_model->get_first_time_in($employee_idno,$date_timelog)->row();
		//last time out
		$lto_data = $trs->Timerecordsummary_model->get_last_time_out($employee_idno,$date_timelog)->row();
		//last time in
		$lti_data = $trs->Timerecordsummary_model->get_last_time_in($employee_idno,$date_timelog)->row();
		//first time out
		$fto_data = $trs->Timerecordsummary_model->get_first_time_out($employee_idno,$date_timelog)->row();
		$fti = $fti_data->time_in;
		$lto = $lto_data->time_out;
		$lti = $lti_data->time_in;
		$fto = $fto_data->time_out;
		//timelogcounterperday
		$multipletimelogcounter = $trs->Timerecordsummary_model->count_timelog($employee_idno,$date_timelog)->num_rows();
		//this will determine if schedule is fixed or flexi
		$st = $trs->Timerecordsummary_model->getschedtype($employee_idno)->row();
		$schedtype = $st->sched_type;
		//this will get the schedule of the employee on work schedule
		$worksched = $trs->Timerecordsummary_model->getschedule($employee_idno)->row();
		// $worksched_day = $worksched->work_sched;
		// print_r(array($lto,$fti));
		$timelogdata = array($employee_idno,$date_timelog,$fti,$lto);
		$alltimelog[] = $timelogdata;
		$get_sched_day = json_decode($worksched->work_sched);
		// print_r($get_sched_day->mon[]);
		//will get the schedule of employee - days
			//constant values
		$wh = $trs->Timerecordsummary_model->get_work_hours($employee_idno)->row();
		$work_hours = $wh->total_whours;
			$breakmins = 60;
			$workingmins = ($work_hours * 60) - $breakmins;
			$late = 0;
			$undertime = 0;
			$overtime = 0;
			$absent = 0;
			$manhours = 0;
		$check_ot = $trs->Timerecordsummary_model->check_overtime($employee_idno,$date_timelog)->row();
		if($check_ot != null){
			$ot_mins = $check_ot->minutes_of_overtime;
		}else{
			$ot_mins = 0;
		}
		//----------------------Will determine the workschedule of the database-------------------------------
		//----------------------Will also provide sched and convert to minutes--------------------------------
		$get_worksched = get_worksched($employee_idno,$date_timelog_day);
		$sched_availability = $get_worksched['sched_availability'];
		$sched_ti_raw = $get_worksched['sched_ti_raw'];
		$sched_to_raw = $get_worksched['sched_to_raw'];
		$sched_ti = $get_worksched['sched_ti'];
		$sched_to = $get_worksched['sched_to'];
		$sched_break_start = $get_worksched['sched_break_start'];
		$sched_break_end = $get_worksched['sched_break_end'];
		$sched_break_mins = $get_worksched['sched_break_mins'];
		//convert actual timein/out to  minutes
		//first time in:
		$actual_timein = convert_to_minutes($fti);
		//last time out:
		$actual_timeout = convert_to_minutes($lto);
		//first time out
		$first_timeout = convert_to_minutes($fto);
		//last time in
		$last_timein = convert_to_minutes($lti);
		//--------------------Will check first the schedule availability of employee-----------------
		switch($sched_availability){
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work schedule-------------------
		case "with_worksched": //check if with work schedule
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
			//checks if employee first time in > $sched_break_end
			if($schedtype == 'fix'){
				//checks if employee first time in > $end schedule
				$check_endshift_timelog = check_endshift_timelog($sched_to,$actual_timein);
				if($check_endshift_timelog == "too_late"){
					continue;
				}
				//checks if employee last time in < $sched_break_start
				$check_startshift_timelog = check_startshift_timelog($sched_ti,$actual_timeout);
				if($check_startshift_timelog == "too_early"){
					continue;
				}
			}
			//checks if employee first time in > first_timeout
			if($actual_timein > $actual_timeout){
				continue;
			}
			//if  multiple timelog
				if($multipletimelogcounter > 1){
				$get_multiple_timelog = $trs->Timerecordsummary_model->get_timelog_multiple($employee_idno,$date_timelog)->result();
				$total_minutes_base= 0;
				//this will get exceeding hours
				$fix_workmins_controller = 0;
				// $lunch_deduction_base = 0;
				$lunch_deduction = 0;
				$alltimeout = array();
				$alltimein = array();
				//for checking of overlaps of ti and to
				$temp_out = 0;
				//checking of exceeding unfiled OT
				$ot_count = 0;
				//this will looop and count all timelogs of employees
				foreach($get_multiple_timelog as $gmt){
					$mt_timeout = $gmt->time_out;
					$mt_timein = $gmt->time_in;
					//converts mo minutes
					//time out
					$mtto_mins = convert_to_minutes($mt_timeout);
					//time in
					$mtti_mins = convert_to_minutes($mt_timein);
					//for checking of overlaps of ti and to
					if($mtti_mins < $temp_out){
						$mtti_mins = $temp_out;
					}
					$temp_out = $mtto_mins;
					//-----------------------------------
					$at_val = array(
						'in' => $mtti_mins,
						'out' => $mtto_mins
					);
					array_push($alltimeout,$mtto_mins);
					array_push($alltimein,$mtti_mins);
					//will check fix schedule if exceeded on sched time out
					if($schedtype == 'fix'){
						if($mtto_mins > $sched_to)
						{
							if($mtti_mins < $sched_to){
								$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $sched_to);
							}else if($mtti_mins >= $sched_to){
								if($check_ot != null){
									$fix_workmins_controller = $fix_workmins_controller + ($mtto_mins - $mtti_mins);
								}else{
									//no OT filed
									$ot_count = $ot_count + ($mtto_mins - $mtti_mins);
									$fix_workmins_controller = $fix_workmins_controller + 0;
								}

							}
						}
					}
					$total_minutes_base = $total_minutes_base + ($mtto_mins - $mtti_mins);
					// print_r($lunch_deduction_base);
				}
				$total_minutes_base = check_total_minutes_base($ot_mins,$ot_count,$total_minutes_base);
				$totalminutes = $total_minutes_base;
				$tm = $totalminutes;
				if($schedtype == 'fix'){
					//end is start break. start is end break
					$gc_start_break = getClosest($sched_break_start,$alltimeout);
					$key = array_search($gc_start_break,$alltimeout);
					$start_break_partner = $alltimein[$key];
					$gc_end_break = getClosest($sched_break_end,$alltimein);
					//returns the break greater thank the start break and prevents returning two equal results
					// if($gc_end_break < $gc_start_break){
					// 	$less_than_to_mins = array_search($gc_end_break,$alltimein);
					// 	unset($alltimein[$less_than_to_mins]);
					// }
					// $gc_end_break = getClosest($sched_break_end,$alltimein);
					$key2 = array_search($gc_end_break,$alltimein);
					$end_break_partner = $alltimeout[$key2];

					$lunch_deduction = get_lunch_schedule($schedtype,$gc_start_break,$sched_break_start,$gc_end_break,$sched_break_end,$start_break_partner,$end_break_partner,$sched_to,$actual_timeout,$employee_idno,$date_timelog);
							//subtract fix lunch break
					$totalminutes = $totalminutes - $lunch_deduction['fix_lunch_break'] - $fix_workmins_controller;
					$workingmins = fix_schedule_hours($sched_to,$sched_ti,$sched_break_start,$sched_break_end);
					$workingmins = negative_checker($workingmins);
					$totalminutes = gettime_computations($totalminutes, $lunch_deduction,$workingmins,$sched_break_mins,$actual_timein,$sched_ti,$actual_timeout,$sched_to)['totalminutes'];
					$totalminutes = negative_checker($totalminutes);
					}
					else if($schedtype == 'flexi'){
						//-------COMPUTATION FOR FLEXI-------
						$overbreak = 0;
						$totalminutes = $totalminutes - $breakmins;
						$totalminutes = negative_checker($totalminutes);
						//totalminutes
						if($totalminutes > $workingmins){
							$totalminutes = $workingmins;
						}
					}
					else{
						$totalminutes = 0;
					}
				}
				//----------------------------single timelog with workorder-----------------------------
				//--------------------------------------------------------------------------------------
				else{
				//will check if time in = time out
				if($actual_timeout == $actual_timein){
					continue;
				}
				//will void time in and out greater than sched_to
				if($sched_to < $actual_timein){
					continue;
				}
				if($schedtype == "fix"){
					//work order values
					$overtime = 0;
					$undertime = 0;
					$late = 0;
					$overbreak = 0;
					//checks if actual_timein is too early
					if($sched_ti > $actual_timein){
						$actual_timein = $sched_ti;
					}
					$totalminutes = ($actual_timeout - $actual_timein)  - $sched_break_mins;
					$totalminutes = gethalfday_mins_single($actual_timeout,$actual_timein,$sched_break_start,$sched_break_end,$sched_break_mins,$totalminutes,$sched_ti);
					$totalminutes = negative_checker($totalminutes);
					//late
					if($sched_ti < $actual_timein){
						$late = $actual_timein - $sched_ti;
					}
					$late = negative_checker($late);
					//undertime
					if($late > 0 ){ //late dumating tapos maaga umuwi
						if($actual_timeout < $sched_to){
							$undertime = $workingmins - ($totalminutes + $late);
						}
					}else{
						if($sched_ti <= $actual_timein){
							if($actual_timeout <= $sched_to){
								$undertime = $workingmins - $totalminutes;
							}else{
								$undertime = $actual_timein - $sched_ti;
							}
						}
					}
					$undertime = negative_checker($undertime);
					$fix_sched_fixer = $totalminutes + $undertime + $late;
					if($fix_sched_fixer >= $workingmins){
						$totalminutes = $workingmins - ($undertime + $late);
					}
					if($totalminutes >= $workingmins){
						$totalminutes = $workingmins;
					}
				}
				else if($schedtype	== "flexi"){
					$overbreak = 0;
					$totalminutes = ($actual_timeout - $actual_timein)  - $breakmins;
					$totalminutes = negative_checker($totalminutes);
					//manhours
					if($totalminutes > $workingmins){
						$totalminutes = $workingmins;
					}
				}
				else{
					$totalminutes = 0;
					}
				}
				return $totalminutes;
		break;
		//-------------------------------------------------------------------------------------------
		//----------------------------------------------Employee has work no schedule-------------------
		case "without_worksched";
		//if with work order
		$getworkorder = $trs->Timerecordsummary_model->get_work_order($employee_idno,$date_timelog)->row();
		//select all dates on workorder and convert it to days. then check it to time record summary if it exist
		//override the time in, time out that is in timerecordsummary_trial that is not in workorder
		if($getworkorder != null){
			$fti = $getworkorder->start_time;
			$lto = $getworkorder->end_time;
			$actual_timein = convert_to_minutes($fti);
			//last time ou:
			// $lto_explode = explode(':', $lto);
			$actual_timeout = convert_to_minutes($lto);
			$totalminutes = ($actual_timeout - $actual_timein) - $breakmins;
			$totalminutes = negative_checker($totalminutes);
			//totalminutes
			if($totalminutes > $workingmins){
				$totalminutes = $workingmins;
			}
			//manhours
			if($totalminutes != 0){
				$totalhours = $totalminutes / 60;
				$manhours = round($totalhours,2);
			}else{
				$manhours = 0;
			}

			return $totalminutes;
			//will check if the overtime is approved, if not. no OT
		}

		else{
		}
				//else with
		break;
		}
		}
		else{
		$noworksched = 0;
		return $noworksched;
				}
			}
}
//-------------End Computations----------------------

function get_first_timein(){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
}
function get_last_time_out(){
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
}

//will check the
function check_overtime($overtime,$scheduled_ot_mins){
	if($overtime > $scheduled_ot_mins){
		$overtime = $scheduled_ot_mins;
	}else{
		$overtime = $overtime;
	}
	return $overtime;
}
function check_total_minutes_base($scheduled_ot,$ot_count,$total_minutes_base){
	//check nito yung sumobrang overtime sa filed OT
	if($scheduled_ot > 0){
		if($ot_count > $scheduled_ot){
			$overtime = $scheduled_ot;
		}else{
			$overtime = $ot_count;
		}
		$tm = $total_minutes_base - $overtime;
	}
	else{

		//walang OT
		$tm = $total_minutes_base - $ot_count;
	}
	return $tm;
}

function getmanhours($totalminutes,$workingmins){
	if($totalminutes > $workingmins){
		$totalminutes = $workingmins;
	}
	$manhours = round(($totalminutes / 60),2);
	return $manhours;
}

//Printing formulas
function compute_dailyrate($total_sal, $frequency){
	if($frequency >= 4){
		$daily_rate = $total_sal;
	}else{
		$daily_rate = ($total_sal * 12) / 365;
	}
	return round($daily_rate,2);
}
        // ROUND(@daily_rate / (e.total_whours - e.total_bhours),2) as hourly_rate,
        // ROUND(@daily_rate / (e.total_whours - e.total_bhours) / 60,2) as min_rate,
function compute_hourlyrate($dailyrate,$total_working_hrs,$total_break_hours){
	$hourly_rate = $dailyrate / ($total_working_hrs - $total_break_hours);
	return round($hourly_rate,2);
}
function compute_minute_rate($dailyrate,$total_working_hrs,$total_break_hours){
	$minute_rate = ($dailyrate / ($total_working_hrs - $total_break_hours)) / 60;
	return round($minute_rate,2);
}
function get_fix_single_timelog(){
	$overtime = 0;
	$undertime = 0;
	$late = 0;
	$totalminutes = ($actual_timeout - $actual_timein) + ($lto_workorder - $fti_workorder) - $sched_break_mins;
	//late
	if($sched_ti < $actual_timein){
		$late = $actual_timein - $sched_ti;
	}
	//undertime
	if($late > 0 ){ //late dumating tapos maaga umuwi
		if($actual_timeout < $sched_to){
			$undertime = $sched_to - $actual_timeout;
		}
	}else{
		if($sched_ti <= $actual_timein){
			if($actual_timeout <= $sched_to){
				$undertime = $workingmins - $totalminutes;
			}else{
				$undertime = $actual_timein - $sched_ti;
			}
		}
	}
}
function negative_checker($val){
	if($val < 0){
		$val = 0;
	}else{
		$val = $val;
	}
	return $val;
}
function select_company_helperx(){
	$ci=& get_instance();
    $ci->load->database();

    $sql = "SELECT * FROM pb_company_helper";
    $query = $ci->db->query($sql);
    return $query->row();
}
function getClosest($search, $arr) {
   $closest = null;
   foreach ($arr as $item) {
      if ($closest === null || abs($search - $closest) > abs($item - $search)) {
         $closest = $item;
      }
   }
   return $closest;
}
function store_current_timelog($arr){
	return $arr;
}

function check_endshift_timelog($sched_to,$actual_timein){
	$var = "";
	if($sched_to < $actual_timein){
		$var = "too_late";
	}else{
		$var = "in";
	}

	return $var;
}
function check_startshift_timelog($sched_ti,$actual_timeout){
	$var = "";
	if($sched_ti > $actual_timeout){
		$var = "too_early";
	}else{
		$var = "in";
	}

	return $var;
}
function validate_holidays($remarks,$employee_idno,$date_timelog){
	//return counted or not_counted
	//check if null
	//check if date > first timelog
	$status = "";
	$trs = get_instance();
	$trs->load->model('time_record/Timerecordsummary_model');
	$date_timelog = strtotime($date_timelog);

	$get_first_timelog = $trs->Timerecordsummary_model->check_first_timelog($employee_idno)->row();
	if($remarks == 1){
		if($get_first_timelog != null){
			$first_date = strtotime($get_first_timelog->date);
			if($date_timelog > $first_date){
				$status = "counted";
			}else{
				$status = "not_counted";
			}
		}else{
			//no timelog yet
			$status = "not_counted";
		}
	}else{
		//employee is absent
		$status = 'not_counted';
	}

	return $status;
}

function search_current_date_1($search,$display_current_date_timerecord){
 	$array_search = array();
 	$valnum = 0;
 	foreach($display_current_date_timerecord as $value){
 	 $s = $search['start_date'];
 	 $e = $search['end_date'];
 	 	if($s <= $display_current_date_timerecord[$valnum]['date_created'] && $e >= $display_current_date_timerecord[$valnum]['date_created']){
 	 		$array_temp = array(
 	 			'searchval' => $display_current_date_timerecord
 	 		);
 	 		array_push($array_search,$array_temp);
 		}
 	$valnum ++;
 	}
 return $array_search;
}
function search_current_date_2($search,$display_current_date_timerecord){
 	$array_search = array();
 	$valnum = 0;
 	foreach($display_current_date_timerecord as $value){
 		$s = $search['datestart_id'];
 		$e = $search['dateend_id'];

 		if($s <= $display_current_date_timerecord[$valnum]['date_created'] && $e >= $display_current_date_timerecord[$valnum]['date_created']){
 			if($display_current_date_timerecord[$valnum]['employee_idno'] == $search['search_id']){
 	 			array_push($array_search,$display_current_date_timerecord[$valnum]);
 			}
 		}

 		$valnum ++;

 	}
 // 	foreach($display_current_date_timerecord as $value){
 // 	 $s = $search['datestart_id'];
 // 	 $e = $search['dateend_id'];
 // 	 	if($s <= $display_current_date_timerecord[$valnum]['date_created'] && $e >= $display_current_date_timerecord[$valnum]['date_created']){
 // 	 		$idnum = $display_current_date_timerecord[$valnum]['employee_idno'];
 // 	 		$search_id = $search['search_id'];
 // 	 		if($search_id != "BVBD1321125"){
 // 	 		}else{
 // 	 		$array_temp = array(
 // 	 			'searchval' => $display_current_date_timerecord
 // 	 		);
 // 	 		array_push($array_search,$array_temp);
 // 	 		}


 // 		}
 // 	$valnum ++;
 // 	}
 	//test123
 	return $array_search;
}
function search_current_date_3($search,$display_current_date_timerecord){
 	$array_search = array();
 	$valnum = 0;
 	foreach($display_current_date_timerecord as $value){
 		$s = $search['datestart_name'];
 		$e = $search['dateend_name'];
 		if($s <= $display_current_date_timerecord[$valnum]['date_created'] && $e >= $display_current_date_timerecord[$valnum]['date_created']){
 			$search_var = preg_replace('/\s+/', '', $search['search_name']);
 			$dataval = preg_replace('/\s+/', '', $display_current_date_timerecord[$valnum]['employee_name']);
 			if(stripos($dataval,$search_var) !== FALSE){
 	 			array_push($array_search,$display_current_date_timerecord[$valnum]);
 			}
 		}
 		$valnum ++;

 	}
 return $array_search;
}
function getDistance($address_from,$address_to_lat,$address_to_lng){
	    // Google API key
	    // $apiKey = 'AIzaSyCp8esu5bFCZDsr9jzWMW-ZxpgeyywXHVM';
	    $apiKey = 'AIzaSyDP_bygsucJ4luo39V5ApXfZbwNigyMnpA';
	    $geoAddress = json_decode($address_from);
	    // Change address format
	    // $formattedAddrFrom    = str_replace(' ', '+', $address_from);
	     // $formattedAddrFrom    = str_replace(',', '', $formattedAddrFrom);
	    // $formattedAddrTo     = str_replace(' ', '+', $try2);

	    // Geocoding API request with start address
	    // $geocodeFrom = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&sensor=false&key='.$apiKey);
	    // $geocodeFrom = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$geoAddress->lat.','.$geoAddress->lng.'&sensor=true_or_false&key='.$apiKey);
	    // $outputFrom = json_decode($geocodeFrom);
	    // if(!empty($outputFrom->error_message)){
	    //     return $outputFrom->error_message;
	    // }
	    // http://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&sensor=true_or_false
	    // Geocoding API request with end address
	    // $geocodeTo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$address_to_lat.','.$address_to_lng.'&sensor=true_or_false&key='.$apiKey);
	    // $outputTo = json_decode($geocodeTo);
	    // if(!empty($outputTo->error_message)){
	    //     return $outputTo->error_message;
	    // }
	    // Get latitude and longitude from the geodata
	    // $latitudeFrom    = $outputFrom->results[0]->geometry->location->lat;
	    $latitudeFrom    = $geoAddress->lat;
	    // $longitudeFrom    = $outputFrom->results[0]->geometry->location->lng;
	    $longitudeFrom    = $geoAddress->lng;
	    // $latitudeTo        = $outputTo->results[0]->geometry->location->lat;
	    $latitudeTo        = $address_to_lat;
	    // $longitudeTo    = $outputTo->results[0]->geometry->location->lng;
	    $longitudeTo    = $address_to_lng;

	    // Calculate distance between latitude and longitude
	    $theta    = $longitudeFrom - $longitudeTo;
	    $dist    = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
	    $dist    = acos($dist);
	    $dist    = rad2deg($dist);
	    $miles    = $dist * 60 * 1.1515;

	    // Convert unit and return distance
	    // $unit = strtoupper($unit);
	    //kilometers
	    $kilometers = round($miles * 1.609344, 2);
	    //converts to meter
	    $meters = round($miles * 1609.344, 2);
	    return $meters;;
	}
