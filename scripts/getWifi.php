<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require '../config/PRTGCredentials.php';

class wifi implements JsonSerializable
{
	public $totalUsers;
	public $name;
	
	function __construct($APName, $sensor)
	{
		updateUbntPoint($sensor,$this);
		$this->name = $APName;
	}
	
	public function jsonSerialize()
	{
		return [
				'name' => $this->name,
				'totalUsers' => $this->totalUsers
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
function updateUbntPoint($sensorid, $object)
{
	$url = 'http://powervault2/api/table.xml?id='.$sensorid.'&content=channels&columns=name,sensor,lastvalue&username='. PRTGCredentials()['user'].'&passhash='.PRTGCredentials()['passwordHash'];
	$string = getSslPage($url);
	$XMLdata = simplexml_load_string($string);
	$object->totalUsers = preg_replace('/\s+|Users/','',$XMLdata->item[4]->lastvalue);
}

$thermostats = array();

$ABldg2East = new wifi("A Building - East 2nd Floor Hallway","3572");
array_push($thermostats, $ABldg2East);


echo json_encode($thermostats);

?>




