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




class Imagen {

	private $_info;
	
	private $_infunc;
	private $_outfunc;
	
	private $_path;
	/**
	 * El path debe ser completo
	 * 
	 * @param unknown_type $path
	 */
	public function __construct($path){
		//Verificar que el archivo existe
		if(!file_exists($path) || !is_readable($path))
			throw new ImagenException("No existe el archivo de imagen");
			
		//Verificar que es una imagen y guardar informacion
		$info = getImageSize($path);
		if(!$info)
			throw new FormatoImagenInvalidoException("No es un tipo de imagen valido");
			
		$this->_info = $info;
		
		switch ($info [2]) {
			case IMAGETYPE_GIF :
				$this->_infunc = 'imagecreatefromgif';
				$this->_outfunc = 'imagegif';
				break;
			case IMAGETYPE_JPEG :
				$this->_infunc = 'imagecreatefromjpeg';
				$this->_outfunc = 'imagejpeg';
				break;
			case IMAGETYPE_PNG :
				$this->_infunc = 'imagecreatefrompng';
				$this->_outfunc = 'imagepng';
				break;
			default :
				throw new FormatoImagenInvalidoException("No es un tipo de imagen valido");
		}
		
		$this->_path = $path;
		
	}
	
	public function getNombre(){
		return basename($this->_path);
	}
	
	public function getWidth(){
		return $this->_info[0];
	}
	
	public function getHeight(){
		return $this->_info[1];
	}
	
	public function getMimeType(){
		return $this->_info['mime'];
	}
	
	/**
	 * 
	 * 
	 * @param unknown_type $path Path completo
	 * @param unknown_type $width
	 * @param unknown_type $height
	 */
	public function crearThumbnail($path, $width, $height){
		
		//verificar que podemos escribir en el destino
		
		$directorio = dirname($path);
		if(!is_writable($directorio))
			throw new ImagenException("No se puede escribir en el directorio de destino");
		
		$funcionCarga = $this->_infunc;
		$imagen_gd = $funcionCarga($this->_path);
		
		if(!$imagen_gd)
			throw new ImagenException("No se puede leer la imagen");
		
		$thumb = ImageCreateTrueColor($width, $height);
		
		ImageCopyResampled($thumb, $imagen_gd, 0,0,0,0, $width, $height, $this->getWidth(), $this->getHeight());
		
		$funcionSalida = $this->_outfunc;
		$funcionSalida($thumb, $path);
		
		if (! file_exists ( $path ))
			throw new ImagenException ( 'Ocurrio un error al crear el thumbnail' );
		if (! is_readable ( $path ))
			throw new ImagenException ( 'No se puede leer el thumbnail' );		
	}
}

?>