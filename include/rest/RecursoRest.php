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




class RecursoRest {
		
	/*
	 * var AbstractController $_controller
	 */
	protected $_controller;
	protected $_exception;
	protected $ok = true;
	
	/**
	 * Calling controller
	 * 
	 * @param AbstractController $controller
	 */
	public function __construct(RestControllerGenerico $controller){
		$this->_controller = $controller;
	}
	

	public function get(Request  $request){
		
	}
	
	public function post(Request $request){
		
	}
	
	public function put(Request $request){
		
	} 
	
	public function delete(Request $request){
		
	}
	
	public function setException(Exception $e){
		$this->_exception = $e;
	}
	
	public function serialize(){
		if(!is_null($this->_exception)){
			$a = array('ok' => false,
				  'error' => $this->_exception->getMessage(),
				  'exception' => $this->_exception
			     );
			return json_encode($a);
		}
		
		$attrib = get_object_vars($this);
		foreach($attrib as $k => $v){
			if(preg_match('/^_/', $k)){
				unset($attrib[$k]);
			} else {
				if(is_object($v) && $v instanceof SerializableArray){
					$attrib[$k] = $attrib[$k]->getAttributesAsArray();
				}
			}
		}
		
		return json_encode($attrib);
	}

}

?>