<?php
/*
Sean Huber CURL library
This library is a basic implementation of CURL capabilities.
It works in most modern versions of IE and FF.
==================================== USAGE ====================================
It exports the CURL object globally, so set a callback with setCallback($func).
(Use setCallback(array('class_name', 'func_name')) to set a callback as a func
that lies within a different class)
Then use one of the CURL request methods:
get($url);
post($url, $vars); vars is a urlencoded string in query string format.
Your callback function will then be called with 1 argument, the response text.
If a callback is not defined, your request will return the response text.

[Informational 1xx]
100="Continue"
101="Switching Protocols"
[Successful 2xx]
200="OK"
201="Created"
202="Accepted"
203="Non-Authoritative Information"
204="No Content"
205="Reset Content"
206="Partial Content"
[Redirection 3xx]
300="Multiple Choices"
301="Moved Permanently"
302="Found"
303="See Other"
304="Not Modified"
305="Use Proxy"
306="(Unused)"
307="Temporary Redirect"
[Client Error 4xx]
400="Bad Request"
401="Unauthorized"
402="Payment Required"
403="Forbidden"
404="Not Found"
405="Method Not Allowed"
406="Not Acceptable"
407="Proxy Authentication Required"
408="Request Timeout"
409="Conflict"
410="Gone"
411="Length Required"
412="Precondition Failed"
413="Request Entity Too Large"
414="Request-URI Too Long"
415="Unsupported Media Type"
416="Requested Range Not Satisfiable"
417="Expectation Failed"
[Server Error 5xx]
500="Internal Server Error"
501="Not Implemented"
502="Bad Gateway"
503="Service Unavailable"
504="Gateway Timeout"
505="HTTP Version Not Supported"

*/

namespace Custom\Util;

class CURL {
	var $callback = false;
	public static $instances = array();
	/**
	 * @var bool 是否在curl内容中返回header信息
	 */
	private $headerEnabled = true;
	
	private $response = array();
	
	private $options = array();

	function getInstance()
	{
    	if(isset(self::$instances[1])) {
	    	$instance = self::$instances[1];
	    	if(!empty($instance) && is_object($instance)) {
	    		return $instance;
	    	}
    	}
    	$instance = new CURL();
    	self::$instances[1] = $instance;
    	return $instance;
	}
	
	function __construct() {
		$this->options[CURLOPT_TIMEOUT] = 30;
		$this->options[CURLOPT_HEADER] = true;
		
		$this->options[CURLOPT_FOLLOWLOCATION] = 1;
		$this->options[CURLOPT_RETURNTRANSFER] = 1;
		$this->options[CURLOPT_COOKIEJAR] = 'cookie.txt';
		$this->options[CURLOPT_COOKIEFILE] = 'cookie.txt';
		
	}
	
	function setCallback($func_name) {
		$this->callback = $func_name;
	}
	function setHeaderEnable($enabled) {
		$this->options[CURLOPT_HEADER] = $enabled;
	}
	function setTimeout($second)
	{
		$this->options[CURLOPT_TIMEOUT] = $second;
	}
	
	function setOption($name, $value) {
		$this->options[$name] = $value;
	}
	
	
	function doRequest($method, $url, $vars = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);

		if(isset($_SERVER['HTTP_USER_AGENT'])) {
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		}
		
		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		}
		
		foreach($this->options as $name => $value) {
			curl_setopt($ch, $name, $value);
		}

		//获得请求状态及数据
		$data = curl_exec($ch);
		$this->response = curl_getinfo($ch);
		$this->response['data'] = $data;
		if(curl_errno($ch)) {
			$this->response['error'] = curl_errno($ch);
		}
		curl_close($ch);

		if ($data) {
			if ($this->callback) {
				$callback = $this->callback;
				$this->callback = false;
				$this->response['data'] = call_user_func($callback, $this->response['data']);
			}
		}
		return $this->response;
	}
	function get($url) {
		return $this->doRequest('GET', $url, null);
	}
	
	
	
	function get_contents($url) {
		$this->setHeaderEnable(false);
		$this->doRequest('GET', $url, null);

		return $this->response['data'];
	}
	
	function getResponseInfo() {
		return $this->response;
	}
	
	/**
	 * 返回curl Errorno
	 */
	function getError() {
		if(isset($this->response['error'])) {
			return $this->response['error'];
		}
		return 0;
	}
	
	function post($url, $vars) {
		return $this->doRequest('POST', $url, $vars);
	}
	
	/**
	 * 下载文件
	 * @param string $url 文件地址
	 * @param string $local    保存到本地的地址
	 * @return bool
	 */
	function download($url, $local) {
	    $state = true;
	    $cp = curl_init($url);
	    $fp = fopen($local , "w");
	    
	    curl_setopt($cp, CURLOPT_FILE, $fp);
	    curl_setopt($cp, CURLOPT_HEADER, 0);
	    
	    if(!curl_exec($cp)){
	        $state = false;
	    }
	    curl_close($cp);
	    fclose($fp);
	    
	    return $state;
	}
}
?>