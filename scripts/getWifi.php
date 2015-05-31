<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require '../config/PRTGCredentials.php';

class wifi implements JsonSerializable
{
	public $name;
	public $sensor;
	public $channelNames;
	public $channelData;
	public $mapID;
	
	function __construct($APName, $sensor,$channelNames,$mapID)
	{
		$this->channelData = updatePoint($sensor,$channelNames);
		$this->name = $APName;
		$this->sensor=$sensor;
		$this->channelNames=$channelNames;
		$this->mapID=$mapID;
	}
	
	public function jsonSerialize()
	{
		return [
				'mapID'=>$this->mapID,
				'name' => $this->name,
				'channelNames' => $this->channelNames,
				'channelData'=>$this->channelData
			];
	}
}

function getSslPage($url) {
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//4031
function updatePoint($sensorid, $items)
{
	$url = 'http://powervault2/api/table.xml?id='.$sensorid.'&content=channels&columns=name,sensor,lastvalue&username='. PRTGCredentials()['user'].'&passhash='.PRTGCredentials()['passwordHash'];
	
	$string = getSslPage($url);
	$XMLdata = simplexml_load_string($string);
	
	$ret = array();
	for($x=0;$x<count($items);$x++)
	{
		for($y=0;$y<count($XMLdata->item);$y++){
			if(strtolower($items[$x]) == strtolower($XMLdata->item[$y]->name))
			{
				array_push($ret,preg_replace('/\s+|Users/','',$XMLdata->item[$y]->lastvalue));
			}
		}
	}
	return $ret;
}

$APs = array();
$row = 0;
if (($handle = fopen("../config/APs.txt", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        $row++;
		$channelNames = array();
        for ($c=3; $c < $num; $c++) {
            array_push($channelNames,$data[$c]);
        }
		array_push($APs,new wifi($data[1],$data[2],$channelNames,$data[0]));
    }
    fclose($handle);
}

echo json_encode($APs);

?>




