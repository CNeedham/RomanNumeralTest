<?php
	$json = file_get_contents('values.json');
	$data = json_decode($json, true);
			
	if (isset($_POST['submit'])) {
		$value = $_POST['value'];
		
		if ($value <= 3999 AND $value > 0) {
			$romanNumerals = array(
				'M'=>1000,
				'CM'=>900,
				'D'=>500,
				'CD'=>400,
				'C'=>100,
				'XC'=>90,
				'L'=>50,
				'XL'=>40,
				'X'=>10,
				'IX'=>9,
				'V'=>5,
				'IV'=>4,
				'I'=>1
			);
			
			$converted = '';		
			while ($value > 0) {
				foreach ($romanNumerals AS $key=>$roman) {
					if ($value >= $roman) {
						$value = $value - $roman;
						$converted .= $key;
						break;
					}
				}
			}
			
			$exists = 0;
			if (count($data) > 0) {
				foreach ($data as $key=>$entry) {
					if ($entry['value'] == $_POST['value']) {
						$exists = 1;
						$data[$key]['time'][] = date('d-m-Y H:i:s');
					}
				}
			} else {
				$data = array();
			}
			
			if ($exists == 0) {
				$data[] = array('value'=>$_POST['value'], 'time'=>array(date('d-m-Y H:i:s')));
			}
					
			$newdata = json_encode($data);
			file_put_contents('values.json', $newdata);
		} else {
			echo '<script>alert("Value is too high. Please choose a value between 1 and 3999.");</script>';
		}
	}
?>

<form name="number" method="POST">
	<input name="value" placeholder="Number to Convert">
	<button name="submit" value="submit">Submit</button>
</form>

<?php
	/* Last Converted */
	if (isset($converted)) {
		echo 'Your Conversion: ' . $converted . '<br />';
	}
	
	if (count($data) > 0) {
		/* Most Converted */
		if (count($data) > 0) {
			$highest = 0;
			$max = 0;
			foreach ($data AS $key=>$val) {
				$count = count($val['time']);
				if ($count >= $highest) {
					if ($count > $highest) {
						$highest = $count;
						$max = $val['value'];
					} else {
						$highest = $count;
						$max = $val['value'] . ' &amp; ' . $max;
					}
				}
			}
			
			echo 'Most Converted Number: ' . $max . ' with ' . $highest . ' searches<br />';
		}
		
		/* Top 10 */
		$values = array();
		foreach ($data AS $key=>$val) {
			$values[$key] = count($val['time']);
		}
		arsort($values);
				
		$num = 1;
		echo '<br /><u>Top 10 Numbers</u><br />';
		foreach ($values AS $key=>$val) {
			if ($num < 11) {
				echo $num . '. ' . $data[$key]['value'] . ' (' . $val . ' times)<br />';
				$num++;
			} else {
				break;
			}
		}
		
		/* Recently Converted */
		$values = array();
		$dates = array();
		$keynum = 0;
		foreach ($data AS $key=>$val) {
			foreach ($val['time'] AS $time) {
				$values[$keynum] = $val['value'];
				$dates[$keynum] = strtotime($time);
				$keynum++;
			}
		}
		arsort($dates);
						
		$num = 0;
		echo '<br /><u>5 Most Recent</u><br />';
		foreach ($dates AS $key=>$date) {
			if ($num < 5) {
				echo $values[$key] . ' (at ' . date('d-m-Y H:i:s', $date) . ')<br />';
				$num++;
			} else {
				break;
			}
		}
	}
?>