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




class EmailValidator extends Validador {
	
	private $nombreCampo = null;
	private $msg;
	
	public function __construct($nombreCampo, $msg = null) {
		$this->nombreCampo = $nombreCampo;
		$this->msg = is_null ( $msg ) ? "No es una direccion de mail válida" : $msg;
	}
	
	protected function validar(array $campos) {
		$errores = array ();
		if (! array_key_exists ( $this->nombreCampo, $campos ) || ! $campos [$this->nombreCampo])
			return $errores;
		
		$valor = $campos [$this->nombreCampo];
		
		require_once "Zend/Validate/EmailAddress.php";
		$validator = new Zend_Validate_EmailAddress();
		if (! $validator->isValid ( $valor )) {
			$error = $this->msg;
//			foreach ( $validator->getMessages () as $message )
//				$this->msg .= $message;
			
			array_push($errores, new ErrorValidacion($this->nombreCampo, $this->msg));
		}
		
		return $errores;
	}
}

class EmailValidatorFactory extends ValidatorFactory {
	
	public function getValidator($field, $attribs){
		return new EmailValidator($field);
	}
	
	public static function getName(){
		return 'Email';
	}
}