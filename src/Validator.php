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

use Moment\Moment;
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
     * @var Rule
     */
    private $_rules = [];
    /**
     * Error del validador
     * @var array 
     */
    private $_errors = [];
    /**
     * Advertencia del validador
     * @var string 
     */
    private $_warnings = [];
    /**
     * Configuración del validador
     * @var array
     */
    private $_config = [];

    /**
     * Crea un objeto validador de datos
     * @param array $config Parámetros para configurar el validador<pre><ul>
     *  <li><b>return_boolean</b>: (boolean) Especifica si las validaciones solo devuelven un valor booleano</li>
     *  <li><b>date_format</b>: (string) Especifica el formato de fecha que se utiliza</li>
     * </ul></pre>
     */
    public function __construct(array $config = []) {
        $this->_config = $config + [
            'return_boolean' => FALSE,
            'date_format' => NULL
        ];
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
                
        if ($errors && $this->_config['return_boolean']) {
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
    public function validRequired($value) {
        if ( !$value && $value !== '0' ) {
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('required');
            }
        }
        return TRUE;
    }
    
    /**
     * Realiza una validación personalizada
     * @param mix $value Valor a verificar
     * @param object $param Función callback
     * @return boolean
     * @throws ValidatorException
     */
    public function validCustom($value, $param) {
        if ( !$param($value) ) {
            if ($this->_config['return_boolean']) {
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
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('unique');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que el valor sea mayor a parámetro dado
     * @param integer $value Valor a verificar
     * @param integer $param Valor mínimo
     * @return boolean
     * @throws ValidatorException
     */
    public function validMinSize($value, $param) {
        if ( $value && $value < $param ) {
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('min_size', Str::bytestostr($param) . '.');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que el valor sea menor a parámetro dado
     * @param integer $value Valor a verificar
     * @param integer $param Valor máximo
     * @return boolean
     * @throws ValidatorException
     */
    public function validMaxSize($value, $param) {
        if ( $value && $value > $param ) {
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('max_size', Str::bytestostr($param) . '.');
            }
        }
        return TRUE;
    }
    
    /**
     * Valida la subida de un archivo
     * @param mix $value Error entregado por el archivo subido
     * @return boolean
     * @throws ValidatorException
     */
    public function validUpload($value) {
        if ( $value ) {
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('upload', Lang::get('validation.valid_upload_error_' . $value));
            }
        }
        return TRUE;
    }
    
    /**
     * Valida que la extensión sea aceptada
     * @param string $value Extensión
     * @param array $param Extensiones permitidas
     * @return boolean
     * @throws ValidatorException
     */
    public function validExtension($value, array $param) {
        if ( $value && !in_array($value, $param) ) {
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('extension');
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que dos valores no sean nulos simultaneamente
     * @param string $value Valor del primer campo
     * @param string $param Valor del segundo campo
     * @return boolean
     * @throws ValidatorException
     */
    public function validRequiredEither($value, $param) {
        if ( !$value && $param ) {
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('required_either');
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
            if ($this->_config['return_boolean']) {
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
            if ($this->_config['return_boolean']) {
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
        if ( $value && !in_array($value, $param) && !key_exists($value, $param) ) {
            if ($this->_config['return_boolean']) {
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
            if ($this->_config['return_boolean']) {
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
            if ($this->_config['return_boolean']) {
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
            if ($this->_config['return_boolean']) {
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
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('exact_length');
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
            if ($this->_config['return_boolean']) {
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
            if ($this->_config['return_boolean']) {
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
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                throw new ValidatorException('exact_val');
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
    public function validMaxDate($value, $param) {
        $date = $this->validDate($value);
        if ( $value && $date->isAfter($param) ) {
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                $date_param = new Moment($param);
                throw new ValidatorException('max_date', $date_param->format($this->_config['date_format']));
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
    public function validMinDate($value, $param) {
        $date = $this->validDate($value);
        if ( $value && $date->isBefore($param) ) {
            if ($this->_config['return_boolean']) {
                return FALSE;
            } else {
                $date_param = new Moment($param);
                throw new ValidatorException('min_date', $date_param->format($this->_config['date_format']));
            }
        }
        return TRUE;
    }
    
    /**
     * Verifica que la fecha esté en un formato correcto
     * @param string $value Fecha a verificar
     * @return boolean
     * @throws ValidatorException
     */
    public function validDate($value) {
        if ( $value ) {
            try {
                return new Moment($value);
            } catch (\Moment\MomentException $ex) {
                if ($this->_config['return_boolean']) {
                    return FALSE;
                } else {
                    $ex->getMessage();
                    throw new ValidatorException('date');
                }
            } catch (\Exception $e) {
                if ($this->_config['return_boolean']) {
                    return FALSE;
                } else {
                    $e->getMessage();
                    throw new ValidatorException('date');
                }
            }
        }
        return TRUE;
    }

    /**
     * Valida las reglas actuales
     * @param array $values Valores a verificar
     * @return boolean
     */
    public function validate(array $values) {
        $return = TRUE;
        foreach ($this->_rules as $field => $rules) {
            foreach ($rules as $rule) {
                try {
                    if ( key_exists($field, $values) ) {
                        $function = 'valid' . Inflector::classify($rule->name);
                        $this->{$function} ( $values[$field], $rule->param );  
                    }
                } catch (ValidatorException $e) {
                    $param = is_object($rule->param) ? 'callback' : (is_array($rule->param) ? Str::natjoin($rule->param) : $rule->param);
                    $message = preg_replace(['/\{field\}/', '/\{value\}/', '/\{param\}/'],
                            [$field, $values[$field], (string)$param], $rule->message ? $rule->message : $e->getMessage());
                    switch ($rule->level) {
                        case Rule::ERROR    :
                            $this->_errors[$field] = $message;
                            $return = FALSE;
                            break;
                        case Rule::WARNING  : 
                            $this->_warnings[$field] = $message;
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
                $this->_rules[$field][$r['rule'] == NULL ? $data[0] : $r['rule']] = new Rule($r['rule'] == NULL ? $data[0] : $r['rule'],
                        $r['param'] == NULL && key_exists(1, $data) ? $data[1] : $r['param'],
                        $r['level'] == NULL && key_exists(2, $data) ? $data[2] : $r['level'],
                        $r['message'] == NULL && key_exists(3, $data) ? $data[3] : $r['message']);
            }
        } else {
            $this->_rules[$field][$rule] = new Rule($rule, $param, $level, $message);
        }

        return $this;
    }
    
    /**
     * Devuelve los errores generados en la validación
     * @return array
     */
    public function getErrors() { 
        return $this->_errors;
    }
    
    /**
     * Devuelve las alertas generadas en la validación
     * @return array
     */
    public function getWarnings() {
        return $this->_warnings;
    }
}
