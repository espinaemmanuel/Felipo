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
 * Valida el largo del texto del campo. Si el campo no existe no devuelve errores.
 *
 */
class ValidadorRangoEntero extends Validador {
	
	private $nombreCampo = null;
	private $msg;
	private $max;
	private $min;
	private $incmin;
	private $incmax;
	
	public function __construct($nombreCampo, $min, $max, $incmin = false, $incmax = false, $msg = null){
		$this->nombreCampo = $nombreCampo;
		$this->msg = is_null($msg)?"Rango requerido [$min, $max]": $msg;
		$this->min = $min;
		$this->max = $max;
		$this->incmax = $incmax;
		$this->incmin = $incmin;
		
	}
	
	protected function validar(array $campos){
		$errores = array();
		if(!array_key_exists($this->nombreCampo, $campos) || !$campos[$this->nombreCampo])
			return $errores;
		
		if(!array_key_exists($this->nombreCampo, $campos))
			return $errores;
			
		$valor = intval($campos[$this->nombreCampo]);
		
		if(!is_null($this->min)){
			if($this->incmin){
				if($valor < intval($this->min)){
					array_push($errores, new ErrorValidacion($this->nombreCampo, $this->msg));
					return $errores;					
				}
			} else {
				if($valor <= intval($this->min)){
					array_push($errores, new ErrorValidacion($this->nombreCampo, $this->msg));
					return $errores;					
				}
			}
		}
		
		if(!is_null($this->max)){
			if($this->incmax){
				if($valor > intval($this->max)){
					array_push($errores, new ErrorValidacion($this->nombreCampo, $this->msg));
					return $errores;					
				}
			} else {
				if($valor >= intval($this->max)){
					array_push($errores, new ErrorValidacion($this->nombreCampo, $this->msg));
					return $errores;					
				}
			}
		}
		
		return $errores;
		
	}
}