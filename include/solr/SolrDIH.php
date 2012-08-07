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




class SolrDIH {
	
	private $host;
	
	public function __construct($host){
		$this->host = $host;
	}
	
	public function fullImport($clean = true, $commit = true, $optimize = true){
		$clean = $clean == true?'true':'false';
		$commit = $commit == true?'true':'false';
		$optimize = $optimize == true?'true':'false';
		
		$queryString = "command=full-import&clean=$clean&commit=$commit&optimize=$optimize";
		return $this->execute($queryString);
	}	
	
	protected function execute($queryString){
		return $this->getJson($this->host.'?'.$queryString);
	}
	
	protected function getJson($uri){
		
		Loader::getInstance()->requireOnce('lib/Zend/Http/Client.php');
		Loader::getInstance()->requireOnce('lib/Zend/Uri/Http.php');
		Loader::getInstance()->requireOnce('lib/Zend/Http/Client/Adapter/Socket.php');
					
		$client = new Zend_Http_Client($uri, array('maxredirects' => 0, 'timeout' => 20));
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