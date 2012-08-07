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



class EnvironmentResolver {
	
	protected $_rules;
	protected $_default;
	protected $_selected;
	
	public function loadRules(){
		$environment = Loader::getInstance()->includeFileAndGetVariables('conf/environments.php', 'environments');
		if(!is_array($environment) || count($environment) == 0){
			Logger::logError("Environments not defined");
		}
		
		$this->_rules = $environment;
	}
	
	public function resolve(){
		foreach($this->_rules as $key => $rule){
			if(isset($rule['default']) && $rule['default'] == true){
				$this->_default = $key;
			}
			if(isset($rule['http_host']) && $rule['http_host'] === $_SERVER ['HTTP_HOST']){
				$this->_selected = $key;
			}
		}
		
		if(!isset($this->_selected)){
			$this->_selected = $this->_default;
		}
		
		if(!isset($this->_selected)){
			Logger::logError("Could not find a valid environment");
			return false;
		}
		
		return $this->_selected;	
	}
}

?>