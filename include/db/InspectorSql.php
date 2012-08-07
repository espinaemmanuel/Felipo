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




class InspectorSql {
	private $_conexion;
	private $_infoBase=array();
	
	private $_arrayTexto = array('date', 'time', 'timestamp', 'datetime', 'char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext');
	private $_arrayFecha = array('date', 'time', 'timestamp', 'datetime');
	
	
	public function __construct(DbConnection $conexion){
		$this->_conexion = $conexion;
	}
	
	private function getInfoTabla($tabla){
		if(!array_key_exists($tabla, $this->_infoBase)){
			$infoTabla = $this->_conexion->query("DESC $tabla");
			for($i=0; $i<count($infoTabla); $i++){
				$infoTabla[$i]['Type'] = eregi_replace("[^a-z]", '', $infoTabla[$i]['Type']);
			}
			$this->_infoBase[$tabla] = $infoTabla;
		}
		
		return $this->_infoBase[$tabla];
	}

	private function getInfoCampo($tabla, $campo){
		
		foreach($this->getInfoTabla($tabla) as $infoCampo){
			if($infoCampo['Field'] === $campo)
				return 	$infoCampo;
		}
		
		throw new SqlException("El campo $campo no existe en la tabla $tabla");
		
	}
	/**
	 * Devuelve true si el campo es de texto y debe agregarse entre comillas
	 * 
	 * @param unknown_type $tabla
	 * @param unknown_type $campo
	 */
	
	public function esTexto($tabla, $campo){
		
		$infoCampo = $this->getInfoCampo($tabla, $campo);
		
		
		return in_array($infoCampo['Type'], $this->_arrayTexto);
		
		/*
		
		foreach($this->_arrayTexto as $texto){
			if(eregi("^$texto", $infoCampo['Type']))
				return true;
		}
		*/
				
		return false;		
	}
	
	public function esFecha($tabla, $campo){
		
		$infoCampo = $this->getInfoCampo($tabla, $campo);
		
		foreach($this->_arrayFecha as $texto){
			if(eregi("^$texto", $infoCampo['Type']))
				return true;
		}
				
		return false;		
	}
	
	public function existe($tabla, $campo){

		foreach($this->getInfoTabla($tabla) as $infoCampo){
			if($infoCampo['Field'] === $campo)
				return 	true;
		}
		
		return false;		

	}
	
}