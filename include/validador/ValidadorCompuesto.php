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




/**
 * Contiene un conjunto de validadores. Para realizar la validacion ejecuta todos los
 * validadores contenidos, y devuelve una lista con todos los errores
 *
 */

class ValidadorCompuesto extends Validador {
	
	private $validaciones = array();
	
	/**
	 * Enter description here...
	 *
	 * @param Validador $v
	 * @return Validador El validador agregado
	 */
	public function agregar(Validador $v){
		if($v === null)
			return null;
			
		array_push($this->validaciones, $v);
		return $v;
	}
	
	protected function validar(array $campos){
		$errores = array();
		foreach($this->validaciones as $validacion)
			$errores = array_merge($errores, $validacion->esValido($campos));
		return $errores;			
	}
}