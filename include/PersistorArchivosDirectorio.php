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




class PersistorArchivosDirectorio {
	

	private $_path;
	
	public function __construct($path){
		$this->_path = $path;
		if(!Loader::getInstance()->fileExists($path))
			throw new PersistorArchivosException("No existe el directorio $path");	
	}
	
	public function persistir(Archivo $archivo){

		if(!is_writable(Loader::getInstance()->getFullPath($this->_path)))
			throw new PersistorArchivosException("El destino no tiene permisos de escritura: $this->_path");
			
		do{
			$nombre = uniqid();
		} while (Loader::getInstance()->fileExists("$this->_path/$nombre"));
		
		$fullName = Loader::getInstance()->getFullPath("$this->_path/$nombre");
		
		if(!move_uploaded_file($archivo->getNombreReal(), $fullName))
			throw new PersistorArchivosException("No se pudo mover el archivo al directorio de destino");
		
		return $fullName;	
	}
}