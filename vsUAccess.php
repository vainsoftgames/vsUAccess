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

    private function callAPI($method, $url, $data = false) {
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
    
    
    // Doors
    public function door_list(){
    	$data = [];
    	
    	return $this->callAPI('GET', '/api/v1/developer/doors', $data);
    }
    public function door_unlock($doorID){
    	return $this->callAPI('PUT', '/api/v1/developer/doors/'. $doorID .'/unlock');
    }
    public function door_lock($doorID){
    	return $this->callAPI('PUT', '/api/v1/developer/doors/'. $doorID .'/lock');
    }

	public function user_addPIN($uID, $pin){
		$data = [];
		$data['pin_code'] = (string)$pin;

		return $this->callAPI('PUT', '/api/v1/developer/users/'. $uID .'/pin_codes', $data);
	}
}
?>
