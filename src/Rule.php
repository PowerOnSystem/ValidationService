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

/**
 * Description of Rules
 * @author Lucas Sosa
 */
class Rule {
    /**
     * Nombre de la regla
     * @var (string) 
     */
    public $name;
    /**
     * Los parametros de la regla
     * @var mix 
     */
    public $param;
    /**
     * Nivel de 
     * @var (string) 
     */
    public $level;
    /**
     * Mensaje de error personalizado
     * @var (string) 
     */
    public $message;
    /**
     * Si es un parámetro dinámico
     * @var (boolean)
     */
    public $dinamic = FALSE;
    
    /**
     * Tipo de regla de error
     */
    const ERROR = 0;
    /**
     * Tipo de regla de advertencia
     */
    const WARNING = 1;
    
    /**
     * Reglas ed validación disponibles
     */
    const RULES = [
        'required', 
        'required_either',
        'options', 
        'compare', 
        
        'min_length', 
        'max_length',
        'exact_length', 
        'range_length',
        
        'min_val', 
        'max_val', 
        'exact_val', 
        'range_val',
        
        'date',
        'min_date', 
        'max_date',
        'range_date',
        
        'date_time', 
        'min_date_time',
        'max_date_time',
        'range_date_time',
        
        'time',
        'min_time',
        'max_time',
        'range_time',

        'min_date_field', 
        'max_date_field', 
        
        'min_date_time_field', 
        'max_date_time_field',
        
        'min_time_field', 
        'max_time_field',
        
        'url', 
        'email',
        'extension', 
        'json',
        'max_size', 
        'min_size', 
        'unique',
        'string_allow', 
        'string_deny', 
        'custom', 
        'upload',
        'number',
        'decimal'
    ];
    
    /**
     * Validación de caracteres de cadena (string)
     */
    const STRING_RULES = [
        'alpha', 
        'numbers', 
        'spaces', 
        'low_strips', 
        'mid_strips', 
        'dots', 
        'commas', 
        'punctuation',
        'quotes', 
        'symbols'
    ];
    
    const STRING_RULE_USERNAME = ['alpha', 'numbers', 'low_strips'];
    const STRING_RULE_NAME = ['alpha', 'dots', 'quotes', 'spaces'];
    const STRING_RULE_ADDRESS = ['alpha', 'numbers', 'quotes', 'spaces', 'dots', 'commas', 'mid_strips'];
    const STRING_RULE_PHONE = ['numbers', 'spaces', 'symbols', 'mid_strips'];
    
    /**
     * Crea una nueva regla de validación
     * <pre>
     * <table width=100% border=1>
     *  <tr><td>Regla</td><td>Parámetros</td><td>Ejemplo</td></tr>
     *  <tr><td>required</td><td>(boolean) TRUE | FALSE</td><td>TRUE</td></tr>
     *  <tr><td>required_either</td><td>(string|array) nombres de los campos requeridos</td>
     *   <td>Cualquiera de las opciones es requerida</td></tr>
     *  <tr><td>options</td><td>(array) with available options</td><td>['option1', 'option2']</td></tr>
     *  <tr><td>compare</td><td>(mix) valor a comparar</td><td>fixed value</td></tr>
     * 
     *  <tr><td>min_length</td><td>(integer) number of length</td><td>10</td></tr>
     *  <tr><td>max_length</td><td>(integer) number of length</td><td>10</td></tr>
     *  <tr><td>exact_length</td><td>(integer) number of length</td><td>10</td></tr>
     *  <tr><td>range_length</td><td>(array)</td><td>[min_length, max_length]</td></tr>
     * 
     *  <tr><td>min_val</td><td>(integer) number of min value</td><td>5</td></tr>
     *  <tr><td>max_val</td><td>(integer) number of max value</td><td>500</td></tr>
     *  <tr><td>exact_val</td><td>(integer) number of length</td><td>10</td></tr>
     *  <tr><td>range_val</td><td>(array) 2 elementos</td><td>[min_val, max_val]</td></tr>
     * 
     *  <tr><td>date</td><td>(string) date format</td><td>d/m/Y</td></tr>
     *  <tr><td>min_date</td><td>(string) min date</td><td>08/09/2018</td></tr>
     *  <tr><td>max_date</td><td>(string) max date</td><td>09/09/2018</td></tr>
     *  <tr><td>range_date</td><td>(array) 2 elementos</td><td>[min_date, max_date]</td></tr>
     * 
     *  <tr><td>date_time</td><td>(string) date_time format</td><td>d/m/Y H:i</td></tr>
     *  <tr><td>min_date_time</td><td>(string) min date</td><td>08/09/2018 10:00</td></tr>
     *  <tr><td>max_date_time</td><td>(string) max date</td><td>09/09/2018 15:00</td></tr>
     *  <tr><td>range_date_time</td><td>(array) 2 elementos</td><td>[min_date_time, max_date_time]</td></tr>
     * 
     *  <tr><td>time</td><td>(string) time format</td><td>d/m/Y H:i</td></tr>
     *  <tr><td>min_time</td><td>(string) min date</td><td>2017-9-8</td></tr>
     *  <tr><td>max_time</td><td>(string) max date</td><td>2017-9-17</td></tr>
     *  <tr><td>range_time</td><td>(array) 2 elementos</td><td>[min_time, max_time]</td></tr>
     * 
     *  <tr><td>min_date_time_field</td><td>(string) field</td><td>field_name</td></tr>
     *  <tr><td>max_date_time_field</td><td>(string) field</td><td>field_name</td></tr>
     * 
     *  <tr><td>min_date_field</td><td>(string) field</td><td>field_name</td></tr>
     *  <tr><td>max_date_field</td><td>(string) field</td><td>field_name</td></tr>
     * 
     *  <tr><td>min_time_field</td><td>(string) field</td><td>field_name</td></tr>
     *  <tr><td>max_time_field</td><td>(string) field</td><td>field_name</td></tr>
     * 
     *  <tr><td>url</td><td>(boolean) TRUE | FALSE</td><td>TRUE</td></tr>
     *  <tr><td>email</td><td>(boolean) TRUE | FALSE</td><td>TRUE</td></tr>
     *  <tr><td>json</td><td>(boolean) TRUE | FALSE</td><td>TRUE</td></tr>
     *  <tr><td>upload</td><td>(boolean) TRUE | FALSE</td><td>TRUE</td></tr>
     *  <tr><td>extension</td><td>(array) with available options</td><td>['jpg', 'bmp', 'gif', 'pdf']</td></tr>
     *  <tr><td>max_size</td><td>(integer) bytes number of max size</td><td>5000</td></tr>
     *  <tr><td>min_size</td><td>(integer) bytes number of min size</td><td>50</td></tr>
     *  <tr><td>number</td><td>(boolean) TRUE | FALSE</td><td>TRUE</td></tr>
     *  <tr><td>decimal</td><td>(boolean|integer) TRUE | FALSE | decimals number</td><td>TRUE | 2</td></tr>
     *  <tr><td>unique</td><td>(array) with options to compare</td><td>['option1', 'option2']</td></tr>
     *  <tr><td>(string)_allow y (string)_deny</td>
     *      <td>(array)
     *      <ul>
     *          <li>symbols <i>Símbolos</i></li>
               <li>quotes <i>Comillas simples o dobles</i></li>
               <li>punctuation <i>Signos de admiración y exclamación</i></li>
               <li>commas <i>Comas</i></li>
               <li>dots <i>Puntos</i></li>
               <li>mid_strips <i>Guiones bajos</i></li>
               <li>low_strips <i>Guiones medios</i></li>
               <li>spaces <i>Espacios en blanco</i></li>
               <li>numbers <i>Números</i></li>
               <li>alpha <i>Letras</i></li>
     *      </ul>
     *  </td><td>['dots', 'alpha', 'spaces']</td></tr>
     *  <tr><td>custom</td><td>callback function(value, param)</td><td>function(value, param){ return TRUE; }</td></tr>
     * </table>
     * </pre>
     * @param string $name La regla
     * @param mix $param Los parametros de la regla seleccionada
     * @param string $message [Opcional] Texto que reemplazará el mensaje de error
     * @throws ValidatorException
     */
    public function __construct( $name, $param, $level = NULL, $message = '') {
        if ( !in_array($name, self::RULES) ) {
            throw new \InvalidArgumentException(sprintf('No se reconoce la regla de validación (%s)', $name));
        }
        $this->validateRule($name, $param);
        $this->name = $name;
        
        $this->param = in_array($name, ['min_date', 'max_date', 'min_time', 'max_time', 'min_date_time', 'max_date_time']) 
            ? new \DateTime($param)
            : (in_array($name, ['range_date', 'range_date_time', 'range_time'])
                ? array_map(function($date) { return new \DateTime($date); }, $param)
                : $param
            );
        
        $this->message = $message;
        $this->level = $level === self::WARNING ? self::WARNING : self::ERROR;
    }

    public function validateRule($name, $param) {
        $function = 'validate' . Inflector::classify($name);
        $this->{$function}($name, $param);
    }
    
    public function validateJson($name, $param) {
        $this->validateRequired($name, $param);
    }
    
    public function validateRequired($name, $param) {
        if ( !is_bool($param) ) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) debe ser condicional', $name));
        }
    }
    
    public function validateNumber($name, $param) {
        $this->validateRequired($name, $param);
    }
    
    public function validateDecimal($name, $param) {
        if ( !is_bool($param) && !is_numeric($param) ) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) debe ser condicional o un número', $name));
        }
    }
    
    public function validateUpload($name, $param) {
        $this->validateRequired($name, $param);
    }
    
    public function validateOptions($name, $param) {
         if ( !$param ) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) debe tener algún elemento', $name));
        }
        if ( !is_array($param) ) {
            throw new \DomainException(sprintf('El parámetro de la regla (%s) debe ser de tipo array', $name));
        }
    }
    
    public function validateRange($name, $param) {
         if ( !$param ) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) debe tener algún elemento', $name));
        }
        if ( !is_array($param) ) {
            throw new \DomainException(sprintf('El parámetro de la regla (%s) debe ser de tipo array', $name));
        }
        if ( count($param) != 2 ) {
            throw new \DomainException(sprintf('El parámetro de la regla (%s) debe tener exactamente 2 elementos (mínimo y máximo)', $name));
        }
    }
    
    public function validateRangeLength($name, $param) {
        $this->validateRange($name, $param);
        if ( count(array_filter($param, 'is_integer')) != 2 ) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) debe tener elementos con valor numérico', $name));
        }
    }
    
    public function validateRangeVal($name, $param) {
        $this->validateRangeLength($name, $param);
    }
    
    public function validateRangeDate($name, $param) {
        $this->validateRange($name, $param);
        if ( count(array_filter($param, function($date) {
            try {
                return new \DateTime($date);
            } catch (\Exception $ex) {
                return FALSE;
            }
        })) != 2 ) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) debe tener elementos con formato de fecha', $name));
        }
        
    }
    
    public function validateRangeDateTime($name, $param) {
        return $this->validateRangeDate($name, $param);
    }
    
    public function validateRangeTime($name, $param) {
        return $this->validateRangeDate($name, $param);
    }
    
    public function validateCompare($name, $param) {
        if ( $param === NULL ) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) no debe ser nulo'), $name);
        }
    }
    
    public function validateMinLength($name, $param) {
        if ( !is_numeric($param) ) {
            throw new \DomainException(sprintf('El parámetro de la regla (%s) debe ser de tipo (integer)', $name));
        }
    }
    
    public function validateMaxLength($name, $param) {
        $this->validateMinLength($name, $param);
    }
    
    public function validateMaxVal($name, $param) {
        $this->validateMinLength($name, $param);
    }
    
    public function validateExactVal($name, $param) {
        $this->validateMinLength($name, $param);
    }
    
    public function validateMinVal($name, $param) {
        $this->validateMinLength($name, $param);
    }
    
    public function validateExactLength($name, $param) {
        $this->validateMinLength($name, $param);
    }
    
    public function validateMinDate($name, $param) {
        try {
            new \DateTime($param);
        } catch (\Exception $ex) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) debe ser una fecha válida, ' . 
                    $ex->getMessage(), $name));
        }
    }
    
    public function validateMaxDate($name, $param) {
        $this->validateMinDate($name, $param);
    }
    
    public function validateMinDateTime($name, $param) {
        $this->validateMinDate($name, $param);
    }
    
    public function validateMaxDateTime($name, $param) {
        $this->validateMinDate($name, $param);
    }
    
    public function validateMinTime($name, $param) {
        $this->validateMinDate($name, $param);
    }
    
    public function validateMaxTime($name, $param) {
        $this->validateMinDate($name, $param);
    }
    
    public function validateMinDateField($name, $param) {
        $this->validateCompare($name, $param);
    }
    
    public function validateMaxDateTimeField($name, $param) {
        $this->validateCompare($name, $param);
    }
    
    public function validateMinDateTimeField($name, $param) {
        $this->validateCompare($name, $param);
    }
    
    public function validateMaxTimeField($name, $param) {
        $this->validateCompare($name, $param);
    }
    
    public function validateMinTimeField($name, $param) {
        $this->validateCompare($name, $param);
    }
    
    public function validateMaxDateField($name, $param) {
        $this->validateCompare($name, $param);
    }
    
    
    public function validateDate($name, $param) {
        $this->validateRequired($name, $param);
    }
    
    public function validateDateTime($name, $param) {
        $this->validateRequired($name, $param);
    }
    
    public function validateTime($name, $param) {
        $this->validateRequired($name, $param);
    }
    
    public function validateUrl($name, $param) {
        $this->validateRequired($name, $param);
    }
    
    public function validateEmail($name, $param) {
        $this->validateRequired($name, $param);
    }
    
    public function validateRequiredEither($name, $param) {
        if ( (!is_string($param) && !is_array($param)) || (is_array($param) && !array_filter($param)) ) {
            throw new \DomainException(sprintf('El parámetro de la regla (%s) debe ser un string o un array con nombres de campo', $name));
        }
    }
    
    public function validateExtension($name, $param) {
        $this->validateCompare($name, $param);
    }
    
    public function validateMinSize($name, $param) {
        $this->validateMinLength($name, $param);
    }
    
    public function validateMaxSize($name, $param) {
        $this->validateMinLength($name, $param);
    }
    
    public function validateUnique($name, $param) {
        if ( !is_array($param) ) {
            throw new \DomainException(sprintf('El parámetro de la regla (%s) debe ser de tipo array', $name));
        }
    }
    
    public function validateCustom($name, $param) {
        if ( !is_object($param) ) {
            throw new \DomainException(sprintf('El parámetro de la regla (%s) debe ser una función', $name));
        }
    }
    
    public function validateStringDeny($name, $param) {
        $this->validateOptions($name, $param);
        $diff = array_diff($param, self::STRING_RULES);
        if ( $diff ) {
            throw new \InvalidArgumentException(sprintf('El parámetro de la regla (%s) posee uno'
                    . ' o más parámetros incorrectos (%s)', $name, implode(', ', $diff)));
        }
    }
    
    public function validateStringAllow($name, $param) {
        $this->validateStringDeny($name, $param);
    }
}
