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




class ArchivoUpload implements Archivo {
	
	private $nombre;
	private $tipo;
	private $size;
	private $archivo_tmp;
	private $archivo;
		
	/**
	 * Enter description here...
	 *
	 * @param array $Archivo
	 */
	public function __construct(array $Archivo){
		
		if(	!array_key_exists('name', $Archivo) ||
			!array_key_exists('type', $Archivo) ||
			!array_key_exists('size', $Archivo) ||
			!array_key_exists('tmp_name', $Archivo) ||
			!array_key_exists('error', $Archivo) )
			throw new ArchivoException('No se paso un array $_FILES válido');
		
		if ($Archivo['error']!=UPLOAD_ERR_OK){
			switch ($Archivo['error']){
				case UPLOAD_ERR_INI_SIZE:
					throw new ArchivoException('El archivo subido exede la directiva upload_max_filesize en php.ini', UPLOAD_ERR_INI_SIZE);
				case UPLOAD_ERR_FORM_SIZE:
					throw new ArchivoException('El archivo subido exede la directiva MAX_FILE_SIZE especificada en el formulario HTML', UPLOAD_ERR_FORM_SIZE);
				case UPLOAD_ERR_PARTIAL:
					throw new ArchivoException('El archivo subido solo fue parcialmente subido', UPLOAD_ERR_PARTIAL);
				case UPLOAD_ERR_NO_FILE:
					throw new ArchivoException('No se subio el archivo', UPLOAD_ERR_NO_FILE);
				case UPLOAD_ERR_NO_TMP_DIR:
					throw new ArchivoException('Falta la carpeta temporal', UPLOAD_ERR_NO_TMP_DIR);
				case UPLOAD_ERR_CANT_WRITE:
					throw new ArchivoException('Fallo la escritura del archivo al disco', UPLOAD_ERR_CANT_WRITE);
				case UPLOAD_ERR_EXTENSION:
					throw new ArchivoException('Subida del archivo detenida por una extension', UPLOAD_ERR_EXTENSION);
				default:
					throw new ArchivoException('Error desconocido');
			}
		}
		
		if ($Archivo['size'] == 0)
			throw new ArchivoException('El tama�o del archivo es cero');
		
		$this->nombre = $Archivo['name'];
		$this->tipo = $Archivo['type'];
		$this->size = filesize($Archivo['tmp_name']);
		$this->archivo_tmp = $Archivo['tmp_name'];
		$this->archivo = fopen($this->archivo_tmp, 'r');
		
	}
	
	public function getNombreReal(){
		return $this->archivo_tmp;
	}
	
	public function getNombre(){
		return $this->nombre;
	}
	
	public function getTipo(){
		return $this->tipo;
	}
	
	public function getSize(){
		return $this->size;
	}
	
	public function read($cantidad){
		return fread($this->archivo, $cantidad);
	}
	
	public function eof(){
		return feof($this->archivo);
	}
}