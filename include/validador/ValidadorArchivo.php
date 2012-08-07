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




class ValidadorArchivo {
	
	private $_largoNombre = null;
	private $_msgLargoNombre;
	
	private $_tipoImagen = null;
	private $_msgTipoImagen;
	
	private $tipos = array(null, 'GIF', 'JPG', 'PNG', 'SWF', 'PSD', 'BMP', 'TIFF', 'TIFF', 'JPC', 'JP2', 'JPX', 'JB2', 'SWC', 'IFF', 'WBMP', 'XBM');
	
	public function setLargoNombre($largo, $msg = null){
		$this->_largoNombre = intval($largo);
		$this->_msgLargoNombre = is_null($msg)?"El nombre del archivo es demasiado largo. Debe tener a lo sumo $largo caracteres":$msg;				
	}
	
	/**
	 * $tipo puede ser GIF, JPG, PNG, SWF, SWC, PSD, TIFF, BMP, IFF, JP2, JPX, JB2, JPC, XBM, o WBMP
	 * Deben estar separados por comas.
	 * 
	 * null es para cualquier tipo de imagen
	 * 
	 *
	 * @param unknown_type $tipo Puede ser 
	 */
	public function setTipoImagen($tipo = null, $msg = null){
		$tipo = is_null($tipo)?'GIF, JPG, PNG, SWF, SWC, PSD, TIFF, BMP, IFF, JP2, JPX, JB2, JPC, XBM, WBMP':$tipo;
		$this->_msgTipoImagen = is_null($msg)?"El archivo debe ser una imagen $tipo":$msg;
		
		$tipo = str_replace(' ', '', $tipo);
		$this->_tipoImagen = explode(',', $tipo);
	}
	
	protected function validar(Archivo $archivo){
		
		$errores = array();
		
		if(!is_null($this->_tipoImagen)){
		
			$info = getImageSize($archivo->getNombreReal());
			if (!$info) {
				array_push($errores, new ErrorValidacion(null, $this->_msgTipoImagen));
				return $errores;
			}
			
			$tipo = $this->tipos[$info[2]];
			$invalido = true;
			
			foreach($this->_tipoImagen as $tipoPermitido){
				if($tipo === $tipoPermitido){
					$invalido = false;
					break;
				}
			}
			
			if($invalido){
				array_push($errores, new ErrorValidacion(null, $this->_msgTipoImagen));
				return $errores;
			}						
		}
		
		if(!is_null($this->_largoNombre)){
			if(strlen($archivo->getNombre())>$this->_largoNombre){
				array_push($errores, new ErrorValidacion(null, $this->_msgLargoNombre));
				return $errores;
			}			
		}
		
		return $errores;			
	}
}