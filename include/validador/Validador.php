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




abstract class ValidatorFactory {
	
	public abstract function getValidator($field, $attribs);
	
	public static function getName() { 
		return null; 
	}
	
}

/**
 * Realiza validaciones en campos que se le pasan en un array.
 *
 */
abstract class Validador {
	
	private $_siguiente = null;
	
	/**
	 * Realiza la validacion y devuelve un array de Errores encontrados.
	 *
	 * @param array $campos Un array asociativo que tiene campos y valores de los mismo
	 * @return array Un array de ErrorValidacion que contiene todos los errores detectados en 
	 * la validacion
	 */
	
	protected abstract function validar(array $campos);
	
	public function esValido(array $campos){
		
		$resultado = $this->validar($campos);
		
		//Implementa chain of responsability
		
		if(count($resultado)==0 && $this->_siguiente!=null){
			return $this->_siguiente->esValido($campos);
		} else {
			return $resultado;			
		}		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Validador $sig
	 * @return Validador El validador puesto como siguiente
	 */
	public function siguiente(Validador $sig){
		$this->_siguiente = $sig;
		return $sig;
	}
}