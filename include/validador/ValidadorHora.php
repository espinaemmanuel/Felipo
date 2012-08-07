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




class ValidadorHora extends Validador {
	
	private $nombreCampo = null;
	private $formato;
	
	/**
	 * Una lista con los formatos posibles y las funciones que se utilizan para realizar la validacion
	 *
	 * @var unknown_type
	 */
	private $formatosPosibles = array ("hh:mm:ss" => "validarHoraPuntos",
									   "hh:mm" => "validarHoraPuntosCorta");
	
	/**
	 * Formatos posibles: dd/mm/aaaa
	 *
	 * @param unknown_type $nombreCampo
	 * @param unknown_type $formato
	 */
	public function __construct($nombreCampo, $formato = "hh:mm:ss"){
		$this->nombreCampo = $nombreCampo;
		$this->formato = $formato;
		
		if(!array_key_exists($formato, $this->formatosPosibles))
			throw new FormatoFechaInvalidoException("El formato de fecha $formato no existe");
	}
	
	protected function validar(array $campos){
		$errores = array();
		if(!array_key_exists($this->nombreCampo, $campos) || !$campos[$this->nombreCampo])
			return $errores;
		
		$funcionValidacion = $this->formatosPosibles[$this->formato];
		$mensaje = $this->$funcionValidacion($campos[$this->nombreCampo]);
		
		if(is_string($mensaje))
			array_push($errores, new ErrorValidacion($this->nombreCampo, $mensaje));
		
		return $errores;			
	}
	
	public static function validarHoraPuntos($hora) {
		if (!ereg('^[0-9]{2}:[0-9]{2}:[0-9]{2}$',$hora)) return "La hora no esta en el formato hh:mm:ss";
		
		list ($horas, $minutos, $segundos) = explode(':', $hora);
		$horaBien = intval($horas)>=0 && intval($horas)<24;
		$minutosBien = intval($minutos)>=0 && intval($minutos)<60;
		$segundoBien = intval($segundos)>=0 && intval($segundos)<24;
		
		if (!$horaBien || !$minutosBien || !$segundoBien) return "La hora ingresada no es valida";

		return true;
	}
	
	public static function validarHoraPuntosCorta($hora) {
		if (!ereg('^[0-9]{2}:[0-9]{2}$',$hora)) return "La hora no esta en el formato hh:mm";
		
		list ($horas, $minutos) = explode(':', $hora);
		$horaBien = intval($horas)>=0 && intval($horas)<24;
		$minutosBien = intval($minutos)>=0 && intval($minutos)<60;
		
		if (!$horaBien || !$minutosBien) return "La hora ingresada no es valida";

		return true;
	}
	
}