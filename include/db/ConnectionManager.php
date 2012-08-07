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




class ConnectionManager {
	
	private $_connections;
	private static $__instance;
	
	private function __construct(){
		$this->_connections = array();
	}

	public static function getInstance(){
		if(!self::$__instance)
			self::$__instance = new ConnectionManager();
			
		return self::$__instance;
	}
	
	public function get($connection = null){
		if(is_null($connection)){
			$connection = 'default';
		}
		
		if(!array_key_exists($connection, $this->_connections)){
			$connConfig = null;
			
			if($connection === 'default'){
				global $config;
				$connConfig = $config['db'];
			} else {
				$connConfig = $config[$connection]['db'];
			}
			
			$this->_connections[$connection] = new DbConnection($connConfig);
		}
		
		return $this->_connections[$connection];
	}
}

?>