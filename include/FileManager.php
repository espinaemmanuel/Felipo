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




class FileManager {
	
	private $_db;
	private $_baseDir;
	private $_tablaArchivos = 'Archivo';
	
	public function __construct(DbConnection $db){
		$this->_db = $db;
	}
	
	public function setTablaArchivo($tabla){
		$this->_tablaArchivos = $tabla;
	}
	
	public function setBaseDir($baseDir){
		if(!Loader::getInstance()->fileExists($baseDir))
			throw new FileNotFoundException("No existe el directorio $baseDir");
		$this->_baseDir = $baseDir;
	}
	
	public function delete($id){
		$registroArchivo = $this->_db->getPorId($this->_tablaArchivos, $id);
		
		if(!$registroArchivo)
			throw new FileNotFoundException("No existe el archivo con la id pasada como parametro");
		
		if($registroArchivo['fileName'] !== '' && Loader::getInstance()->fileExists($this->_baseDir.'/'.$registroArchivo['fileName'])){
			$path = Loader::getInstance()->getFullPath($this->_baseDir.'/'.$registroArchivo['fileName']);
			if (!unlink($path))
				throw new ArchivoException("No se puede eliminar el archivo");
		}
		
		$this->_db->borrar($this->_tablaArchivos, $registroArchivo['id']);
	}
}

?>