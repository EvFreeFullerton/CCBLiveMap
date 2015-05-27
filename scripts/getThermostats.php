<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

require '../config/PRTGCredentials.php';

class thermostat implements JsonSerializable
{
	public $coolPoint;
	public $hotPoint;
	public $runningStatus;
	public $currentTemp;
	public $name;
	
	function __construct($ThermostatName, $sensor)
	{
		updateThermostat($sensor,$this);
		$this->name = $ThermostatName;
	}
	
	public function jsonSerialize()
	{
		return [
				'name' => $this->name,
				'coolPoint' => $this->coolPoint,
				'hotPoint' => $this->hotPoint,
				'runningStatus' => $this->runningStatus,
				'currentTemp' => $this->currentTemp
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
function updateThermostat($sensorid, $object)
{
	$url = 'http://powervault2/api/table.xml?id='.$sensorid.'&content=channels&columns=name,sensor,lastvalue&username='. PRTGCredentials()['user'].'&passhash='.PRTGCredentials()['passwordHash'];
	$string = getSslPage($url);
	$XMLdata = simplexml_load_string($string);
	$object->coolPoint = preg_replace('/\s+|#/','',$XMLdata->item[0]->lastvalue);
	$object->hotPoint = preg_replace('/\s+|#/','',$XMLdata->item[1]->lastvalue);
	$object->runningStatus = $XMLdata->item[2]->lastvalue;
	$object->currentTemp = preg_replace('/\s+|#/','',$XMLdata->item[3]->lastvalue);
}
$thermostats = array();

$worshipCenterLobby = new thermostat("Worship Center Lobby","4025");
array_push($thermostats, $worshipCenterLobby);

$worshipCenterStage = new thermostat("Worship Center Stage","4029");
array_push($thermostats, $worshipCenterStage);

$worshipCenterWest = new thermostat("Worship Center West","4031");
array_push($thermostats, $worshipCenterWest);

$worshipCenterEast = new thermostat("Worship Center East","4027");
array_push($thermostats, $worshipCenterEast);

$choirRoom = new thermostat("Choir Room","4033");
array_push($thermostats, $choirRoom);

$chapel = new thermostat("Chapel","4035");
array_push($thermostats, $chapel);

$greenRoom = new thermostat("Green Room","4037");
array_push($thermostats, $greenRoom);

$fireside = new thermostat("Fireside Room","4039");
array_push($thermostats, $fireside);

$mikesOffice = new thermostat("Mike's Office","4104");
array_push($thermostats, $mikesOffice);

$B110 = new thermostat("B110","4106");
array_push($thermostats, $B110);

$NCAmph = new thermostat("NC Amph","4108");
array_push($thermostats, $NCAmph);




echo json_encode($thermostats);

?>




