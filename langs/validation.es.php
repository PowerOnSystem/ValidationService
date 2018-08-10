<?php
/*
 * Copyright (C) PowerOn Sistemas - Lucas Sosa
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
return [
    'valid_exact_val'           => 'El valor debe ser igual a ({param}).',
    'valid_max_val'             => 'El valor debe ser menor a ({param}).',
    'valid_min_val'             => 'El valor debe ser mayor a ({param}).',
    'valid_range_val'           => 'El valor debe estar entre {param}.',
    
    'valid_exact_length'        => 'La cantidad de caracteres debe ser igual a ({param}).',
    'valid_max_length'          => 'La cantidad de caracteres debe ser menor a ({param}).',
    'valid_min_length'          => 'La cantidad de caracteres debe ser mayor a ({param}).',
    'valid_range_length'        => 'La cantidad de caracteres debe estar entre {param}.',
    
    'valid_min_size'            => 'El tamaño del archivo no debe ser menor a {param}',
    'valid_max_size'            => 'El tamaño del archivo no debe superar los {param}',
    
    'valid_required'            => 'Este campo es obligatorio.',
    'valid_json'                => 'Los datos no se recibieron de forma correcta.',
    'valid_custom'              => 'El valor no es válido.',
    'valid_unique'              => 'El valor ({value}) ya existe, no pueden haber dos registros iguales.',
    
    'valid_extension'           => 'Solo se admiten las extensiones ({param}).',
    'valid_required_either'     => 'Debe completar uno de estos campos.',
    'valid_email'               => 'Debe ser un E-mail válido.',
    'valid_url'                 => 'Debe ser una URL válida (ej: www.webpage.com).',
    'valid_options'             => 'Se encontraron una o más opciones inválidas.',
    'valid_compare'             => 'Debe coincidir con ({param})',
    'valid_number'              => 'Debe ser un número válido.',
    'valid_decimal'             => 'Debe ser un número decimal válido.',
    
    'valid_date'                => 'Debe ser una fecha válida.',
    'valid_max_date'            => 'La fecha debe ser anterior a {param}',
    'valid_min_date'            => 'La fecha debe ser posterior a {param}',
    'valid_range_date'          => 'La fecha debe estar entre {param}',
    
    'valid_date_time'           => 'Debe ser una fecha y hora válida.',
    'valid_max_date_time'       => 'La fecha y hora debe ser anterior a {param}',
    'valid_min_date_time'       => 'La fecha y hora debe ser posterior a {param}',
    'valid_range_date_time'     => 'La fecha y hora debe estar entre {param}',
    
    'valid_time'                => 'Debe ser una hora válida.',
    'valid_max_time'            => 'El horario debe ser anterior a las {param}',
    'valid_min_time'            => 'El horario debe ser posterior a las {param}',
    'valid_range_time'          => 'El horario debe estar entre {param}',
    
    'valid_date_field'          => 'La fecha del campo a comparar es incorrecta.',
    'valid_min_date_field'      => 'La fecha de este campo debe ser posterior a la del campo especificado.',
    'valid_max_date_field'      => 'La fecha de este campo debe ser anterior a la del campo especificado.',
    
    'valid_date_time_field'     => 'La fecha y hora del campo a comparar es incorrecta.',
    'valid_min_date_time_field' => 'La fecha y hora de este campo debe ser posterior a la del campo especificado.',
    'valid_max_date_time_field' => 'La fecha y hora de este campo debe ser anterior a la del campo especificado.',
    
    'valid_time_field'          => 'El horario del campo a comparar es incorrecto.',
    'valid_min_time_field'      => 'El horario de este campo debe ser posterior a la del campo especificado.',
    'valid_max_time_field'      => 'El horario de este campo debe ser anterior a la del campo especificado.',
    
    'valid_upload'              => 'No se pudo subir el archivo.',
    'valid_upload_error_' . UPLOAD_ERR_INI_SIZE    => 'El peso del archivo supera el limite del servidor.',
    'valid_upload_error_' . UPLOAD_ERR_FORM_SIZE   => 'El peso del archivo supera el limite impuesto por el formulario.',
    'valid_upload_error_' . UPLOAD_ERR_PARTIAL     => 'El archivo fue parcialmente subido, intente nuevamente.',
    'valid_upload_error_' . UPLOAD_ERR_NO_FILE     => 'El archivo no se subió al servidor.',
    'valid_upload_error_' . UPLOAD_ERR_NO_TMP_DIR  => 'Falta la carpeta temporal.',
    'valid_upload_error_' . UPLOAD_ERR_CANT_WRITE  => 'No se pudo escribir el fichero en el disco.',
    'valid_upload_error_' . UPLOAD_ERR_EXTENSION   => 'El archivo es potencialmente peligroso para el sistema.',
    
    'valid_string'              => 'Este campo no admite:',
    'valid_string_symbols'      => 'símbolos como (\\ [ ^)',
    'valid_string_quotes'       => 'comillas',
    'valid_string_punctuation'  => 'símbolos de pregunta y admiración',
    'valid_string_commas'       => 'comas',
    'valid_string_dots'         => 'puntos',
    'valid_string_mid_strips'   => 'guiones medios',
    'valid_string_low_strips'   => 'guiones bajos',
    'valid_string_spaces'       => 'espacios en blanco',
    'valid_string_numbers'      => 'números',
    'valid_string_alpha'        => 'letras',
];