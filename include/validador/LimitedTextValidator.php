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
class LimitedTextValidator extends Validador {
	
	private $nombreCampo = null;
	private $max;
	
	public function __construct($nombreCampo, $largo_max){
		$this->nombreCampo = $nombreCampo;
		$this->max = intval($largo_max);
		
		if($this->max <= 0)
			throw new ParametroIncorrectoException("El largo maximo del mensaje debe ser mayor que cero y se paso $largo_max");
	}
	
	protected function validar(array $campos){
		$errores = array();
		if(!array_key_exists($this->nombreCampo, $campos) || !$campos[$this->nombreCampo])
			return $errores;
		
		if(!array_key_exists($this->nombreCampo, $campos))
			return $errores;
			
		if(strlen($campos[$this->nombreCampo]) > $this->max)
			array_push($errores, new ErrorValidacion($this->nombreCampo, "Este campo no puede contener mas de $this->max caracteres"));
		
		return $errores;			
	}
}

class LimitedTextValidatorFactory extends ValidatorFactory {
	
	public function getValidator($field, $attribs){
		return new LimitedTextValidator($field, $attribs['constraints'][LimitedTextValidatorFactory::getName()]['limit']);
	}
	
	public static function getName() {
		return 'Limited';
	}
}