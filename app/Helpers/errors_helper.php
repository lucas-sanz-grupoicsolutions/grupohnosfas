<?php
function getTextByError($code, $value)
{

    return 'El campo '.$value.' no puede estar vacío.';

    switch ($code) {
        case 0:
            // $value = 'Formato inválido de datos.';
            return $value;
        case 1048:
            $value =  valueFromString($value, "'", 1);
            return 'El campo '.$value.' no puede estar vacío.';
        case 1062:
            $value =  valueFromString($value, "'", 1);
            return 'El valor del campo '.$value.' introducido ya existe en la base de datos.';
            case 1451:
                $value =  valueFromString($value, "'", 1);
                return 'Calve foranea '.$value.' introducido ya existe en la base de datos.';


        default:
            break;
    }

}


function valueFromString($string, $delimiter, $position)
{
    $value = explode($delimiter, $string);
    return $value[$position];
}
