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




class ValidadorFecha extends Validador {
	
	private $nombreCampo = null;
	private $formato;
	
	/**
	 * Una lista con los formatos posibles y las funciones que se utilizan para realizar la validacion
	 *
	 * @var unknown_type
	 */
	private $formatosPosibles = array ("dd/mm/aaaa" => "validarFechaBarras");
	
	/**
	 * Formatos posibles: dd/mm/aaaa
	 *
	 * @param unknown_type $nombreCampo
	 * @param unknown_type $formato
	 */
	public function __construct($nombreCampo, $formato = "dd/mm/aaaa"){
		$this->nombreCampo = $nombreCampo;
		$this->formato = $formato;
		
		if(!array_key_exists($formato, $this->formatosPosibles))
			throw new FormatoFechaInvalidoException("El formato de fecha $formato no existe");
	}
	
	protected function validar(array $campos){
		$errores = array();
		if(!array_key_exists($this->nombreCampo, $campos) || !$campos[$this->nombreCampo])
			return $errores;
		
		if(!array_key_exists($this->nombreCampo, $campos) || !$campos[$this->nombreCampo])
			return $errores;
		
		$funcionValidacion = $this->formatosPosibles[$this->formato];
		$mensaje = $this->$funcionValidacion($campos[$this->nombreCampo]);
		
		if(is_string($mensaje))
			array_push($errores, new ErrorValidacion($this->nombreCampo, $mensaje));
		
		return $errores;			
	}
	
	private static function validarFechaBarras($fecha) {
		//Validar fecha
		if (!ereg('^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$', $fecha)) return "La fecha no esta en el formato dd/mm/aaaa";
		
		list($dia, $mes, $year) = explode('/', $fecha);
		if (mktime(0,0,0,$mes, $dia, $year)==false) return 'La fecha ingresada no es valida';
		
		return true;
	}
}