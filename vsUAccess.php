<?php

class vsUAccess {
    private $host;
    private $port;
    private $token;

    public function __construct($host = "https://console-ip", $port = 12445, $token) {
        $this->host = $host;
        $this->port = $port;
        $this->token = $token;
    }

    private function callAPI($method, $url='', $data = false) {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        	case "GET":
           		if ($data)
                	$url = sprintf("%s?%s", $url, http_build_query($data));
            	curl_setopt($curl, CURLOPT_HTTPGET, true);
           		break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_URL, ($this->host . ':' . $this->port . $url));
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
        	'Content-Type: application/json',
        	'Accept: application/json'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Corresponds to --insecure
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($curl);
        if (!$result) {
        	$error = curl_error($curl);
        	curl_close($curl);
        	die("Connection Failure: " . $error);
		}
        curl_close($curl);

        return json_decode($result, true);
    }
    
    public function ping(){
    	return $this->callAPI('GET');
    }

    public function createUser($firstName, $lastName, $employeeNumber, $onboardTime, $userEmail) {
        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'employee_number' => $employeeNumber,
            'onboard_time' => $onboardTime,
            'user_email' => $userEmail
        ];
        return $this->callAPI('POST', "/api/v1/developer/users", $data);
    }

    public function updateUser($id, $firstName, $lastName, $employeeNumber, $onboardTime, $userEmail) {
        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'employee_number' => $employeeNumber,
            'onboard_time' => $onboardTime,
            'user_email' => $userEmail
        ];
        return $this->callAPI('PUT', "/api/v1/developer/users/" . $id, $data);
    }
    
    public function viewUsers($pageNum=1, $pageSize=25, $showPolicy=false){
    	$data = [];
    	if($pageNum > 1) $data['page_num'] = $pageNum;
    	if($pageSize > 0) $data['page_size'] = $pageSize;
    	if($showPolicy) $data['expand'] = ['access_policy'];

    	return $this->callAPI('GET', '/api/v1/developer/users', $data);
    }

    public function getUser($userID){
    	return $this->callAPI('GET', '/api/v1/developer/users/'. $userID);
    }
    
    public function getSysLogs($topic='door_openings'){
    	$data = [];
    	$data['topic'] = $topic;

    	return $this->callAPI('POST', '/api/v1/developer/system/logs', $data);
    }

	public function user_addPIN($uID, $pin){
		$para = [];
		$para['pin_code'] = (string)$pin;

		return $this->callAPI('PUT', '/api/v1/developer/users/'. $uID .'/pin_codes', $para);
	}
    
    
    // Doors
    public function door_list(){
    	$para = [];
    	
    	return $this->callAPI('GET', '/api/v1/developer/doors', $para);
    }
    public function door_unlock($doorID){
    	return $this->callAPI('PUT', '/api/v1/developer/doors/'. $doorID .'/unlock');
    }
    public function door_lock($doorID){
    	return $this->callAPI('PUT', '/api/v1/developer/doors/'. $doorID .'/lock');
    }
    
    
    // Visitors
    public function getVisitors($id=false, $keyword=false){
    	return $this->callAPI('GET', '/api/v1/developer/visitors'. ($id ? ('/'. $id) : ($keyword ? ('?keyword='. $keyword) : '')));
    }
    public function addVisitor($firstName='', $lastName='', $reason='', $startTime=0, $endTime=0){
    	$para = [];
    	$para['first_name'] = $firstName;
    	$para['last_name'] = $lastName;
    	$para['visit_reason'] = (in_array($reason, ['Interview','Business','Cooperation','Others']) ? $reason : 'Others');
    	$para['start_time'] = (is_numeric($startTime) ? $startTime : strtotime($startTime));
    	$para['end_time'] = (is_numeric($endTime) ? $endTime : strtotime($endTime));
    	
    	
    	return $this->callAPI('POST', '/api/v1/developer/visitors', $para);
    }
    public function updateVisitorBulk($id, $para){
    	return $this->callAPI('PUT', '/api/v1/developer/visitors/'. $id, $para);
    }
    public function updateVisitor($id, $field, $value){
    	$para = [];
    	$para[$field] = $value;
    	
    	return $this->updateVisitorBulk($id, $para);
    }
}
?>
