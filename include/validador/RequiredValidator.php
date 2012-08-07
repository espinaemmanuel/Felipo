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




class RequiredValidator extends Validador {
	
	private $nombreCampo = null;
	private $msg;
	
	public function __construct($nombreCampo, $msg = null){
		$this->nombreCampo = $nombreCampo;
		$this->msg = is_null($msg)?"Campo requerido": $msg;
	}
	
	protected function validar(array $campos){
		$errores = array();
		if(!array_key_exists($this->nombreCampo, $campos) || strlen($campos[$this->nombreCampo])==0)
			array_push($errores, new ErrorValidacion($this->nombreCampo, $this->msg));
		return $errores;			
	}
}

class RequiredValidatorFactory extends ValidatorFactory {
	
	public function getValidator($field, $attribs){
		return new RequiredValidator($field);
	}
	
	public static function getName(){
		return 'Required';
	}
	
}