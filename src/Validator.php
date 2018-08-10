<?php

/*
 * Copyright (C) PowerOn Sistemas
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PowerOn\Validation;

use PowerOn\Utility\Inflector;
use PowerOn\Utility\Str;
use PowerOn\Utility\Lang;

/**
 * Validador de campos de un formulario
 * @author Lucas Sosa
 * @version 0.1
 */
class Validator {
    /**
     * Reglas
     * @var Rule[]
     */
    private $rules = [];
    /**
     * Error del validador
     * @var array 
     */
    private $errors = [];
    /**
     * Advertencia del validador
     * @var string 
     */
    private $warnings = [];
    /**
     * Configuración del validador
     * @var array
     */
    private $config = [];
    /**
     * Valores a verificar
     * @var array
     */
    private $values = [];
    /**
     * Crea un objeto validador de datos
     * @param array $config Parámetros para configurar el validador
     * <table border=1>
     *  <tr><td><b>return_boolean</b></td><td> (boolean) false</td>
     *   <td>Especifica si las validaciones solo devuelven un valor booleano</td></tr>
     *  <tr><td><b>date_format</b></td><td> (string) d/m/Y</td><td> Especifica el formato de fecha que se utiliza</td>
     *  <tr><td><b>date_time_format</b></td><td> (string) d/m/Y H:i</td><td> Especifica el formato de fecha y hora que se utiliza</td>
     *  <tr><td><b>time_format</b></td><td> (string) H:i</td><td> Especifica el formato de hora que se utiliza</td>
     *  <tr><td><b>langs_dir</b></td><td> (string) ./langs </td>
     *   <td>Directorio donde estan almacenados los lenguajes del validador</td>
     *  <tr><td><b>lang</b></td><td> (string) es</td>
     *   <td> Lenguaje del validador, debe existir el archivo {langs_dir}/validation.{lang}.php</td>
     * </table>
     */
    public function __construct(array $config = []) {
        $this->config = $config + [
            'return_boolean' => FALSE,
            'date_format' => 'd/m/Y',
            'date_time_format' => 'd/m/Y H:i',
            'time_format' => 'H:i',
            'langs_dir' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'langs' . DIRECTORY_SEPARATOR,
            'lang' => 'es'
        ];
        
        Lang::load('validation', $this->config['lang'], $this->config['langs_dir']);
    }
    
    /**
     * Verifica existencia de caracteres alfabéticos
     * @param string $value
     * @return boolean
     */
    public function validStringAlpha($value) {
        return preg_match('/[a-zá-ú]/i', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de números
     * @param string $value
     * @return boolean
     */
    public function validStringNumbers($value) {
        return preg_match('/[0-9]/', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de espacios
     * @param string $value
     * @return boolean
     */
    public function validStringSpaces($value) {
        return preg_match('/ /', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de guiones bajos
     * @param string $value
     * @return boolean
     */
    public function validStringLowstrips($value) {
        return preg_match('/_/', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de guiones medios
     * @param string $value
     * @return boolean
     */
    public function validStringMidstrips($value) {
        return preg_match('/-/', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de puntos
     * @param string $value
     * @return boolean
     */
    public function validStringDots($value) {
        return preg_match('/\./', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de comas
     * @param string $value
     * @return boolean
     */
    public function validStringCommas($value) {
        return preg_match('/\,/i', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de signos de admiración e interrogación
     * @param string $value
     * @return boolean
     */
    public function validStringPunctuation($value) {
        return preg_match('/\¿|\?|\¡|\!/', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de comillas
     * @param string $value
     * @return boolean
     */
    public function validStringQuotes($value) {
        return preg_match('/\'|\"/i', $value) ? TRUE : FALSE;
    }
    
    /**
     * Verifica la existencia de simbolos poco comunes
     * @param string $value
     * @return boolean
     */
    public function validStringSymbols($value) {
        return preg_match('/[^\w|[a-zá-úÁ-Ú | |\ |\&|\*|\(|\)|\:|\+|\.|\,|\]|\[|\-|\;|\/|\?|\!|\¿|\¡|\'|\"|\#|\%|\$|@]/i', $value)
                ? TRUE : FALSE;
    }
    
    /**
     * Valida una cadena en base a una serie de parametros
     * @param boolean $is_allow Condición de verificación, TRUE quiere decir que 
     * la cadena solo admite los parámetros enviados, y FALSE lo contrario
     * @param string $value
     * @param array $param Parámetros para verificar la cadena
     * @see Rules::STRING_RULES
     * @throws ValidatorException
     * @return boolean
     */
    private function validStringMode($is_allow, $value, $param) {
        $errors = [];
        foreach (Rule::STRING_RULES as $p) {
            $function = 'validString' . Inflector::classify($p);
            $is_match = $this->$function($value);
            $is_requested = in_array($p, $param);
            $result = $is_requested ? ($is_allow ? TRUE : !$is_match) : ($is_allow ? !$is_match : TRUE);
            if ( !$result ) {
                $errors[] = $p;
            }
        }
                
        if ($errors && $this->config['return_boolean']) {
            return FALSE;
        } else if ($errors) {
            $translated_errors = [];
            foreach ($errors as $e) {
                $translated_errors[] = Lang::get('validation.valid_string_' . $e);
            }
            throw new ValidatorException('string', implode(', ', $translated_errors));
        }
        return TRUE;
    }
    
    /**
     * Valida que una cadena de texto contenga los parametros enviados
     * @param string $value
     * @param array $param Parámetros para verificar la cadena
     * @see Rules::STRING_RULES
     * @throws ValidatorException
     * @return boolean
     */
    public function validStringAllow($value, $param) {
        return $this->validStringMode(TRUE, $value, $param);
    }
    
    /**
     * Valida que una cadena de texto no contega los parametros enviados
     * @param string $value
     * @param array $param Parámetros para verificar la cadena
     * @see Rules::STRING_RULES
     * @throws ValidatorException
     * @return boolean
     */
    public function validStringDeny($value, $param) {
        return $this->validStringMode(FALSE, $value, $param);
    }
    
    /**
     * Valida que el valor no sea nulo
     * @param mix $value Valor a verificar
     * @return boolean
     * @throws ValidatorException
     */
    public function validRequired($value, $param, $return_boolean = FALSE) {
        if ( $param && ((is_array($value) && !array_filter($value))
                || (!$value && $value !== '0')) 
            ) {
            if ($this->config['return_boolean'] || $return_boolean) {
                return FALSE;
            } else {
                throw new ValidatorException('required');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que el valor no sea nulo
     * @param mix $value Valor a verificar
     * @return boolean
     * @throws ValidatorException
     */
    public function validNumber($value, $param) {
        if ( $value && $param && !is_numeric($value)) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('number');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que el valor no sea nulo
     * @param mix $value Valor a verificar
     * @return boolean
     * @throws ValidatorException
     */
    public function validDecimal($value, $param) {
        $expr = '/^[0-9]+\.' . (is_numeric($param) ? '[0-9]{' . $param . '}' : '[0-9]+') . '$/';
        if ( $value && $param && !preg_match($expr, $value) ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('decimal');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que dos o más valores no sean nulos simultaneamente
     * @param string $value Valor del primer campo
     * @param string|array $param Valor de los campos subsiguientes
     * @return boolean
     * @throws ValidatorException
     */
    public function validRequiredEither($value, $param) {
        if ( !$this->validRequired($value, TRUE, TRUE) 
                && (
                    (is_array($param) && !array_filter($param, function($p){ 
                        return key_exists($p, $this->values) && $this->validRequired($this->values[$p], TRUE, TRUE);
                    }))
                    || (
                        !is_array($param) 
                        && (!key_exists($param, $this->values) || !$this->validRequired($this->values[$param], TRUE, TRUE)) 
                    ) 
                )
        ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('required_either');
            }
        }
        return TRUE;
    }
    
    /**
     * Realiza una validación personalizada
     * @param mix $value Valor a verificar
     * @param callable $param Función callback
     * @return boolean
     * @throws ValidatorException
     */
    public function validCustom($value, callable $param) {
        if ( !$param($value, $this->values) ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('custom');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que el valor sea único en una lista sin repetición
     * @param mix $value Valor a verificar
     * @param array $param Lista a comprar que no exista el valor.
     * @return boolean
     * @throws ValidatorException
     */
    public function validUnique($value, $param) {
        if ( in_array($value, $param) ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('unique');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que el valor sea mayor a parámetro dado
     * @param integer $file Valor a verificar
     * @param integer $param Valor mínimo en bytes
     * @return boolean
     * @throws ValidatorException
     */
    public function validMinSize($file, $param) {
        if ( is_array($file) && is_numeric(key($file)) ){
            $result = TRUE;
            foreach ($file as $f) {
                $result = $this->validMinSize($f, $param);
                if (!$result) {
                    break;
                }
            }
            
            return $result;
        }
        
        if ( !is_array($file) || !key_exists('size', $file) || $file['size'] < $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('min_size');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que el valor sea menor a parámetro dado
     * @param integer $file Valor a verificar
     * @param integer $param Valor máximo en bytes
     * @return boolean
     * @throws ValidatorException
     */
    public function validMaxSize($file, $param) {
        if ( is_array($file) && is_numeric(key($file)) ){
            $result = TRUE;
            foreach ($file as $f) {
                $result = $this->validMaxSize($f, $param);
                if (!$result) {
                    break;
                }
            }
            
            return $result;
        }
        
        if ( !is_array($file) || !key_exists('size', $file) || $file['size'] > $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('max_size');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida la subida de un archivo
     * @param mix $file Error entregado por el archivo subido
     * @return boolean
     * @throws ValidatorException
     */
    public function validUpload($file, $param) {
        if (!$file || !$param) {
            return TRUE;
        }
        
        if ( is_array($file) && is_numeric(key($file)) ) {
            $return = TRUE;
            foreach ($file as $f) {
                $return = $this->validUpload($f, $param);
                if (!$return) {
                    break;
                }
            }
            
            return $return;
        }
        
        if ( !is_array($file) 
                || !key_exists('tmp_name', $file)
                || !file_exists($file['tmp_name'])
                || !key_exists('error', $file)
                || $file['error']
        ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException(
                    !is_array($file) || !key_exists('error', $file) || !$file['error']
                        ? 'upload'
                        : 'upload_error_' . $file['error']
                );
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que la extensión sea aceptada
     * @param string $file Archivo subido
     * @param string|array $param Extensiones permitidas
     * @return boolean
     * @throws ValidatorException
     */
    public function validExtension($file, $param) {
        if (!$file) {
            return TRUE;
        }
        
        if ( is_array($file) && is_numeric(key($file)) ){
            $result = TRUE;
            foreach ($file as $f) {
                $result = $this->validExtension($f, $param);
                if (!$result) {
                    break;
                }
            }
            
            return $result;
        }
        
        $extension = NULL;
        if ( is_array($file) && key_exists('name', $file) ) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            d($extension);
        }
                
        if ( !$extension 
                || (is_array($param) && !in_array($extension, $param)) 
                || (!is_array($param) && $extension != $param) 
        ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('extension');
            }
        }
        return TRUE;
    }
        
    /**
     * Valida una dirección de correo electrónico
     * @param string $value Correo electrónico
     * @return boolean
     * @throws ValidatorException
     */
    public function validEmail($value) {
        if ( $value && !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $value) ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('email');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida una URL
     * @param string $value La URL
     * @return boolean
     * @throws ValidatorException
     */
    public function validUrl($value) {
        if ( $value && !preg_match('/^([a-z0-9\.-]+)\.([a-z\.]{2,6})([\/\w\?=.-]*)*\/?$/i', $value) ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('url');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que la opción elegida se encuentre en la lista
     * @param mix $value Opción elegida
     * @param array $param Opciones disponibles
     * @return boolean
     * @throws ValidatorException
     */
    public function validOptions($value, array $param) {
        if ( $value && 
                ( 
                    (is_array($value) && count(array_intersect($value, $param)) != count($value))
                    || (!is_array($value) && !in_array($value, $param))
                )
        ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('options');
            }
        }
        return TRUE;
    }
    
    /**
     * Compara dos valores
     * @param mix $value Valor uno a comparar
     * @param mix $param Valor dos a comprar
     * @return boolean
     * @throws ValidatorException
     */
    public function validCompare($value, $param) {
        if ( $value != $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('compare');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la cantidad de caracteres sea mayor a la establecida en los parámetros
     * @param string $value Valor a verificar
     * @param integer $param Cantidad de caracteres mínima
     * @return boolean
     * @throws ValidatorException
     */
    public function validMinLength($value, $param) {
        if ( $value && strlen($value) < $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('min_length');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la cantidad de caracteres sea menor a la establecida en los parámetros
     * @param string $value Valor a verificar
     * @param integer $param Cantidad de caracteres máxima
     * @return boolean
     * @throws ValidatorException
     */
    public function validMaxLength($value, $param) {
        if ( $value && strlen($value) > $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('max_length');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la cantidad de caracteres sea igual a la establecida en los parámetros
     * @param string $value Valor a verificar
     * @param integer $param Cantidad de caracteres
     * @return boolean
     * @throws ValidatorException
     */
    public function validExactLength($value, $param) {
        if ( $value && strlen($value) != $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('exact_length');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la cantidad de caracteres sea igual a la establecida en los parámetros
     * @param string $value Valor a verificar
     * @param array $param Rango de caracteres
     * @return boolean
     * @throws ValidatorException
     */
    public function validRangeLength($value, $param) {
        if ( $value && (strlen($value) < $param[0] || strlen($value) > $param[1]) ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('range_length');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que el valor sea menor al establecido en los parámetros
     * @param string $value Valor a verificar
     * @param integer $param Valór máximo
     * @return boolean
     * @throws ValidatorException
     */
    public function validMaxVal($value, $param) {
        if ( $value !== NULL && $value > $param) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('max_val');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que el valor sea mayor al establecido en los parámetros
     * @param string $value Valor a verificar
     * @param integer $param Valór mínimo
     * @return boolean
     * @throws ValidatorException
     */
    public function validMinVal($value, $param) {
        if ( $value !== NULL && $value < $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('min_val');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que el valor sea igual al establecido en los parámetros
     * @param string $value Valor a verificar
     * @param integer $param Valór a comparar
     * @return boolean
     * @throws ValidatorException
     */
    public function validExactVal($value, $param) {
        if ( $value !== NULL && $value != $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('exact_val');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que el valor sea igual al establecido en los parámetros
     * @param int $value Valor a verificar
     * @param array $param Rango a comparar
     * @return boolean
     * @throws ValidatorException
     */
    public function validRangeVal($value, $param) {
        if ( $value !== NULL && ($value < $param[0] || $value > $param[1])  ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('range_val');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la fecha sea menor a la establecida en los parámetros
     * @param string $value Fecha a verificar
     * @param string $param Fecha máxima
     * @return boolean
     * @throws ValidatorException
     */
    public function validMaxDate($value, $param, $format = NULL, $exception = 'date') {
        $date = $this->validDate($value, $format ?: $this->config['date_format'], $exception);
        if ( $value && $date > $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('max_' . $exception);
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la fecha sea mayor a la establecida en los parámetros
     * @param string $value Fecha a verificar
     * @param string $param Fecha mínima
     * @return boolean
     * @throws ValidatorException
     */
    public function validMinDate($value, $param, $format = NULL, $exception = 'date') {
        $date = $this->validDate($value, $format ?: $this->config['date_format'], $exception);
        if ( $value && $date < $param ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                
                throw new ValidatorException('min_' . $exception);
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la fecha sea mayor a la establecida en los parámetros
     * @param string $value Fecha a verificar
     * @param string $param Fecha mínima
     * @return boolean
     * @throws ValidatorException
     */
    public function validMinDateTime($value, $param) {
        return $this->validMinDate($value, $param, $this->config['date_time_format'], 'date_time');
    }
    
    /**
     * Verifica que la fecha sea menor a la establecida en los parámetros
     * @param string $value Fecha a verificar
     * @param string $param Fecha maxima
     * @return boolean
     * @throws ValidatorException
     */
    public function validMaxDateTime($value, $param) {
        return $this->validMaxDate($value, $param, $this->config['date_time_format'], 'date_time');
    }
    
    /**
     * Verifica que la fecha sea mayor a la establecida en los parámetros
     * @param string $value Fecha a verificar
     * @param string $param Fecha mínima
     * @return boolean
     * @throws ValidatorException
     */
    public function validMinTime($value, $param) {
        return $this->validMinDate($value, $param, $this->config['time_format'], 'time');
    }
    
    /**
     * Verifica que la fecha sea menor a la establecida en los parámetros
     * @param string $value Fecha a verificar
     * @param string $param Fecha maxima
     * @return boolean
     * @throws ValidatorException
     */
    public function validMaxTime($value, $param) {
        return $this->validMaxDate($value, $param, $this->config['time_format'], 'time');
    }
    
    /**
     * Verifica que la fecha este en un rango establecido en los parámetros
     * @param string $value Fecha a verificar
     * @param array $param Rango de fechas
     * @return boolean
     * @throws ValidatorException
     */
    public function validRangeDate($value, $param, $format = NULL, $exception = 'date') {
        $date = $this->validDate($value, $format ?: $this->config['date_format'], $exception);
        if ( $value && ($date < $param[0] || $date > $param[1]) ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('range_' . $exception);
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la fecha este en un rango establecido en los parámetros
     * @param string $value Fecha a verificar
     * @param array $param Rango de fechas
     * @return boolean
     * @throws ValidatorException
     */
    public function validRangeDateTime($value, $param) {
        return $this->validRangeDate($value, $param, $this->config['date_time_format'], 'date_time');
    }
    
    /**
     * Verifica que la fecha este en un rango establecido en los parámetros
     * @param string $value Fecha a verificar
     * @param array $param Rango de fechas
     * @return boolean
     * @throws ValidatorException
     */
    public function validRangeTime($value, $param) {
        return $this->validRangeDate($value, $param, $this->config['time_format'], 'time');
    }
    
    /**
     * Verifica que la fecha esté en un formato correcto
     * @param string $value Fecha a verificar
     * @return \DateTime
     * @throws ValidatorException
     */
    public function validDate($value, $format, $exception = 'date', $return_boolean = FALSE) {
        if ( $value ) {
            $date = \DateTime::createFromFormat($format, $value);
            if (!$date && ($this->config['return_boolean'] || $return_boolean)) {
                    return FALSE;
            } else if (!$date) {
                throw new ValidatorException($exception);
            }
            
            return $date;
        }
        return TRUE;
    }
    
    /**
     * Verifica que la fecha sea mayor a la fecha de un campo dado
     * @param string $value Fecha del campo
     * @param string $param Nombre del campo a obtener la fecha
     * @return \DateTime
     * @throws ValidatorException
     */
    public function validMinDateField($value, $param, $format = NULL, $exception = 'date') {
        $date2 = $this->validDate(
            key_exists($param, $this->values) ? $this->values[$param] : NULL, 
            $format ?: $this->config['date_format'], 
            $exception, 
            TRUE
        );
        
        if ( !$date2 ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException($exception . '_field');
            }
        }
        
        try {
            return $this->validMinDate($value, $date2, $format, $exception);
        } catch (ValidatorException $e) {
            throw new ValidatorException($e->getName() != $exception ? 'min_' . $exception . '_field' : $exception);
        }
    }
    
    /**
     * Verifica que la fecha sea menor a la fecha de un campo dado
     * @param string $value Fecha del campo
     * @param string $param Nombre del campo a obtener la fecha
     * @return \DateTime
     * @throws ValidatorException
     */
    public function validMaxDateField($value, $param, $format = NULL, $exception = 'date') {
        $date2 = $this->validDate(
            key_exists($param, $this->values) ? $this->values[$param] : NULL, 
            $format ?: $this->config['date_format'], 
            $exception, 
            TRUE
        );
        
        if ( !$date2 ) {
            if ($this->config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException($exception . '_field');
            }
        }
        
        try {
            return $this->validMaxDate($value, $date2, $format, $exception);
        } catch (ValidatorException $e) {
            throw new ValidatorException('max_' . $exception . '_field');
        }
    }
    
    /**
     * Verifica que la fecha y hora sea mayor a la fecha de un campo dado
     * @param string $value Fecha del campo
     * @param string $param Nombre del campo a obtener la fecha
     * @return \DateTime
     * @throws ValidatorException
     */
    public function validMinDateTimeField($value, $param) {
        return $this->validMinDateField($value, $param, $this->config['date_time_format'], 'date_time');
    }
    
    /**
     * Verifica que la fecha y hora sea mayor a la fecha de un campo dado
     * @param string $value Fecha del campo
     * @param string $param Nombre del campo a obtener la fecha
     * @return \DateTime
     * @throws ValidatorException
     */
    public function validMaxDateTimeField($value, $param) {
        return $this->validMaxDateField($value, $param, $this->config['date_time_format'], 'date_time');
    }
    
    /**
     * Verifica que la hora sea mayor a la fecha de un campo dado
     * @param string $value Fecha del campo
     * @param string $param Nombre del campo a obtener la fecha
     * @return \DateTime
     * @throws ValidatorException
     */
    public function validMinTimeField($value, $param) {
        return $this->validMinDateField($value, $param, $this->config['time_format'], 'time');
    }
    
    /**
     * Verifica que la hora sea mayor a la fecha de un campo dado
     * @param string $value Fecha del campo
     * @param string $param Nombre del campo a obtener la fecha
     * @return \DateTime
     * @throws ValidatorException
     */
    public function validMaxTimeField($value, $param) {
        return $this->validMaxDateField($value, $param, $this->config['time_format'], 'time');
    }
    
    /**
     * Verifica que la hora sea mayor a la fecha de un campo dado
     * @param string $value Fecha del campo
     * @param string $param Nombre del campo a obtener la fecha
     * @return \DateTime
     * @throws ValidatorException
     */
    public function validJson($value, $param) {
        if ($value && $param) {
            if (json_last_error() !== JSON_ERROR_NONE && $this->config['return_boolean']) {
                return FALSE;
            } elseif (json_last_error() !== JSON_ERROR_NONE ) {
                throw new ValidatorException('json');
            }
        }
    }
    
    /**
     * Valida las reglas actuales
     * @param array $values Valores a verificar
     * @return boolean
     */
    public function validate(array $values) {
        $return = TRUE;
        $this->values = $values;
        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                try {
                    if ( key_exists($field, $values) ) {
                        $function = 'valid' . Inflector::classify($rule->name);
                        $this->{$function} ( $values[$field], $rule->param );  
                    }
                } catch (ValidatorException $e) {
                    $config = $this->config;
                    
                    $param = is_callable($rule->param) 
                        ? 'callback' 
                        : (is_array($rule->param) && !in_array($rule->name, ['range_date', 'range_date_time', 'range_time'])
                            ? Str::natjoin($rule->param) 
                            : (in_array($rule->name, ['range_date', 'range_date_time', 'range_time'])
                                ? Str::natjoin(array_map(function(\DateTime $date) use ($rule, $config) {
                                        return $date->format(
                                            $rule->name == 'range_date'
                                                ? $config['date_format']
                                                : ($rule->name == 'range_date_time'
                                                    ? $config['date_time_format']
                                                    : $config['time_format']
                                                )
                                        );
                                    }, $rule->param))
                                : ( in_array($rule->name, ['min_date', 'max_date', 'min_date_time', 'max_date_time', 'min_time', 'max_time'])
                                    ? $rule->param->format(
                                        in_array($rule->name, ['min_date', 'max_date'])
                                            ? $config['date_format']
                                            : (in_array($rule->name, ['min_date_time', 'max_date_time'])
                                                ? $config['date_time_format']
                                                : $config['time_format']
                                            )
                                        )
                                    : ( in_array($rule->name, ['min_size', 'max_size'])
                                        ? Str::bytestostr($rule->param)
                                        : $rule->param
                                    )
                                )
                            )
                        );
                    $message = preg_replace(['/\{field\}/', '/\{value\}/', '/\{param\}/'], [
                            $field, 
                            is_array($values[$field]) ? Str::natjoin($values[$field]) : $values[$field],
                            (string)$param
                        ], $rule->message ? $rule->message : $e->getMessage()
                    );
                    
                    switch ($rule->level) {
                        case Rule::ERROR    :
                            $this->errors[$field] = $message;
                            $return = FALSE;
                            break;
                        case Rule::WARNING  : 
                            $this->warnings[$field] = $message;
                            break;
                    }
                }
            }
        }
        
        return $return;
    }
        
    /**
     * Agrega una regla de validación
     * @param string $field El nombre del campo a agregar la regla
     * @param string|array $rule El nombre de la regla o un array con varias reglas ejemplo: <pre>
     * <code>$rule = [ [rule_name, params, level, message], [...] ];</code></pre>
     * @param mix $param Los parámetros de la regla
     * @param string $level [Opcional] El nivel de alerta, Si es ERROR no procede, si es WARNING solo genera una advertencia
     * @param string $message [Opcional] Texto que reemplazará el mensaje de error
     * @return \PowerOn\Validation\Validator
     */
    public function add($field, $rule, $param = NULL, $level = NULL, $message = NULL) {
        if ( is_array($rule) ) {
            foreach ($rule as $data) {
                $r = [
                    'rule' => NULL,
                    'param' => $param,
                    'level' => $level,
                    'message' => $message
                ] + $data
                        ;
                $this->rules[$field][$r['rule'] == NULL ? $data[0] : $r['rule']] = new Rule($r['rule'] == NULL ? $data[0] : $r['rule'],
                        $r['param'] == NULL && key_exists(1, $data) ? $data[1] : $r['param'],
                        $r['level'] == NULL && key_exists(2, $data) ? $data[2] : $r['level'],
                        $r['message'] == NULL && key_exists(3, $data) ? $data[3] : $r['message']);
            }
        } else {
            $this->rules[$field][$rule] = new Rule($rule, $param, $level, $message);
        }

        return $this;
    }
    
    /**
     * Devuelve los errores generados en la validación
     * @return array
     */
    public function getErrors() { 
        return $this->errors;
    }
    
    /**
     * Devuelve las alertas generadas en la validación
     * @return array
     */
    public function getWarnings() {
        return $this->warnings;
    }
}
