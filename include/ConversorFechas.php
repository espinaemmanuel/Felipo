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




class ConversorFechas {
	
	public static function SqlDate_to_CompletaCastellano($fecha) {
		if(!$fecha)
			return;
			
		if (!ereg('^[0-9]{4}-[0-9]{2}-[0-9]{2}$', $fecha))
			throw new ConversorFechasException("La fecha $fecha no es tiene el formato DATE");
		
		$fechaArray = explode('-', $fecha);
		$years = intval($fechaArray[0]);
		$meses = intval($fechaArray[1]);
		$dias = intval($fechaArray[2]);
		
		$mesesPalabras = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo',
							   'Junio', 'Julio', 'Agosto', 'Septiembre',
							   'Octubre', 'Noviembre', 'Diciembre' );
		
		return sprintf('%d de %s de %d', $dias, $mesesPalabras[$meses - 1], $years);		
	}
	
	public static function SqlDatetime_to_CompletaCastellano($fecha) {
		if(!$fecha)
			return;
			
		if (!ereg('^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$', $fecha))
			throw new ConversorFechasException("La fecha $fecha no es tiene el formato DATE");
		
		$fechaArray = explode(' ', $fecha);
		$dia = $fechaArray[0];
		$hora = $fechaArray[1];
		
		$horaArray = explode(':', $hora);
		$horaFormato = $horaArray[0].':'.$horaArray[1].' hs';
		
		return self::SqlDate_to_CompletaCastellano($dia).' '.$horaFormato;
	}
	
	public static function SqlDate_to_CortaBarras($fecha, $longYear=true) {
		if(!$fecha)
			return;
			
		if (!ereg('^[0-9]{4}-[0-9]{2}-[0-9]{2}$', $fecha))
			throw new ConversorFechasException("La fecha $fecha no es tiene el formato DATE");
		
		$fechaArray = explode('-', $fecha);
		$years = intval($fechaArray[0]);
		$meses = intval($fechaArray[1]);
		$dias = intval($fechaArray[2]);
		
		if(!$longYear)
			$years = $years%100;
		
		return $dias.'/'.$meses.'/'.$years;		
	}
	
	public static function stardard_to_CortaBarras($date_string){
		$date = new DateTime($date_string);
		return $date->format('d-m-Y');
	}
	
		
	public static function CortaBarras_to_SqlDate($fecha) {
		if(!$fecha)
			return;
			
		if (!ereg('^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$', $fecha))
			throw new ConversorFechasException("La fecha $fecha no es tiene el formato DATE");
		
		$fechaArray = explode('/', $fecha);
		$years = intval($fechaArray[2]);
		$meses = intval($fechaArray[1]);
		$dias = intval($fechaArray[0]);
		
		return $years.'-'.$meses.'-'.$dias;		
	}
}
