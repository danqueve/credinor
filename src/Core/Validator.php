<?php
// src/Core/Validator.php
namespace App\Core;

use PDO;

class Validator
{
    /**
     * Validates $data against $rules. Returns array of errors, empty if valid.
     *
     * Rule formats:
     *   'required'
     *   ['min', 5]
     *   ['max', 100]
     *   ['len', 7, 11]        — min and max length
     *   ['in', ['a','b']]
     *   ['numeric']
     *   ['gt', 0]             — greater than
     *   ['gte', 0]            — greater than or equal
     *   ['lte', 100]          — less than or equal
     *   ['date']
     *   ['date_not_past']     — date >= today
     *   ['unique', 'table', 'column', $excludeId]
     *   ['not_equals_field', 'other_field']
     *   ['gte_field', 'other_field']
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $strValue = (string) ($value ?? '');

            foreach ($fieldRules as $rule) {
                $ruleName = is_array($rule) ? $rule[0] : $rule;

                switch ($ruleName) {
                    case 'required':
                        if ($strValue === '' || $value === null) {
                            $errors[$field] = 'Este campo es obligatorio.';
                        }
                        break;

                    case 'numeric':
                        if ($strValue !== '' && !is_numeric($strValue)) {
                            $errors[$field] = 'Debe ser un número válido.';
                        }
                        break;

                    case 'gt':
                        if ($strValue !== '' && is_numeric($strValue) && (float)$strValue <= (float)$rule[1]) {
                            $errors[$field] = "Debe ser mayor a {$rule[1]}.";
                        }
                        break;

                    case 'gte':
                        if ($strValue !== '' && is_numeric($strValue) && (float)$strValue < (float)$rule[1]) {
                            $errors[$field] = "Debe ser mayor o igual a {$rule[1]}.";
                        }
                        break;

                    case 'lte':
                        if ($strValue !== '' && is_numeric($strValue) && (float)$strValue > (float)$rule[1]) {
                            $errors[$field] = "Debe ser menor o igual a {$rule[1]}.";
                        }
                        break;

                    case 'min':
                        if ($strValue !== '' && is_numeric($strValue) && (float)$strValue < (float)$rule[1]) {
                            $errors[$field] = "El valor mínimo es {$rule[1]}.";
                        }
                        break;

                    case 'max':
                        if ($strValue !== '' && is_numeric($strValue) && (float)$strValue > (float)$rule[1]) {
                            $errors[$field] = "El valor máximo es {$rule[1]}.";
                        }
                        break;

                    case 'len':
                        $len = mb_strlen($strValue);
                        if ($strValue !== '' && ($len < (int)$rule[1] || $len > (int)$rule[2])) {
                            $errors[$field] = "Debe tener entre {$rule[1]} y {$rule[2]} caracteres.";
                        }
                        break;

                    case 'in':
                        if ($strValue !== '' && !in_array($strValue, $rule[1], true)) {
                            $errors[$field] = 'Valor no permitido.';
                        }
                        break;

                    case 'date':
                        if ($strValue !== '' && strtotime($strValue) === false) {
                            $errors[$field] = 'Fecha inválida.';
                        }
                        break;

                    case 'date_not_past':
                        if ($strValue !== '' && strtotime($strValue) !== false && $strValue < date('Y-m-d')) {
                            $errors[$field] = 'La fecha no puede ser anterior a hoy.';
                        }
                        break;

                    case 'unique':
                        // ['unique', 'table', 'column', $excludeId (optional)]
                        $table     = $rule[1];
                        $column    = $rule[2];
                        $excludeId = $rule[3] ?? null;

                        if ($strValue !== '') {
                            $db  = Database::getInstance();
                            $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
                            $params = [$strValue];
                            if ($excludeId !== null) {
                                $sql .= ' AND id != ?';
                                $params[] = $excludeId;
                            }
                            $stmt = $db->prepare($sql);
                            $stmt->execute($params);
                            if ((int)$stmt->fetchColumn() > 0) {
                                $errors[$field] = "Ya existe un registro con ese valor.";
                            }
                        }
                        break;

                    case 'not_equals_field':
                        $other = $data[$rule[1]] ?? null;
                        if ($strValue !== '' && $strValue === (string)$other) {
                            $errors[$field] = 'No puede ser igual al campo relacionado.';
                        }
                        break;

                    case 'gte_field':
                        $other = (float)($data[$rule[1]] ?? 0);
                        if ($strValue !== '' && is_numeric($strValue) && (float)$strValue < $other) {
                            $errors[$field] = "Debe ser mayor o igual al campo relacionado.";
                        }
                        break;
                }

                // Stop checking further rules for this field if already has error
                if (isset($errors[$field])) {
                    break;
                }
            }
        }

        return $errors;
    }

    public static function throwIfInvalid(array $data, array $rules): void
    {
        $errors = self::validate($data, $rules);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
