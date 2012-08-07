<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */




class SolrQuery {
	
	private $_params = array();
	private $_filters = array();
	
	private $host;
	
	public function __construct($host){
		$this->host = $host;
		$this->_params['wt'] = 'json';
		$this->_params['q']= '';
		$this->_params['omitHeader']= 'true';		
	}
	
	public function setQuery($queryString){
		if(!$queryString)
			return;
		
		$this->_params['q'] = $queryString;		
	}
	
	public function escapeSpecialChars($string, $includeSpaces = true){
		$special_chars = array('+', '-', '&&', '||', '!', '(', ')', '{', '}',
							   '[', ']', '^', '"', '~', '*', '?', ':');
		
		if($includeSpaces){
			array_push($special_chars, ' ');
		}
		$string = str_replace('\\', "\\\\", $string);
		foreach($special_chars as $char){
			$string = str_replace($char, "\\$char", $string); 
		}
		
		return $string;
	}
	
	public function addFilter($field, $value){
		if(!$value)
			return;
			
		if(is_array($value)){
			foreach($value as $v){
				$this->_addFilter($field, $v);
			}
		} else {
			$this->_addFilter($field, $value);
		}	
	}
	
	protected function _addFilter($field, $value){
		if(!$value)
			return;
		
		if(!array_key_exists($field, $this->_filters)){
			$this->_filters[$field] = array();
		}
		
		$value = urldecode($value);
		
		array_push($this->_filters[$field], $this->escapeSpecialChars($value));		
	}
	
	public function setOffset($offset){
		if(!$offset)
			return;
			
		$this->_params['start'] = intval($offset);		
	}
	
	public function setNumResults($num){
		if(!$num)
			return;
			
		$this->_params['rows'] = intval($num);
	}
	
	public function setSort($field, $sort = 'desc'){
		if(!$field)
			return;
					
		$this->_params['sort'] = $field.' '.$sort;
	}
	
	public function execute(){
		$query = http_build_query($this->_params);
		$filter = '&';
		foreach($this->_filters as $field => $values){
			foreach($values as $v){
				$filter.= "fq=$field:(".rawurlencode($v).")&";				
			}
		}
		
		$query .= $filter;
		
		return $this->getJson($this->host.'?'.$query);
	}
	
	protected function getJson($uri){
		
		Loader::getInstance()->requireOnce('lib/Zend/Http/Client.php');
		Loader::getInstance()->requireOnce('lib/Zend/Uri/Http.php');
		Loader::getInstance()->requireOnce('lib/Zend/Http/Client/Adapter/Socket.php');
					
		$client = new Zend_Http_Client($uri, array('maxredirects' => 0, 'timeout' => 5));
		$response = $client->request();
		
		if($response->isError()){
			throw new SolrQueryException("Error quering server: ". $response->getStatus().' - '.$response->getMessage());
		}

		if($response->isRedirect()){
			throw new SolrQueryException("Unexpected redirection ocurred");
		}
		
		$json = json_decode($response->getBody(), true);
	
		if(is_null($json)){
			throw new SolrQueryException("Can't decode response json format");
		}
		
		return $json;
		
	}

}

?>