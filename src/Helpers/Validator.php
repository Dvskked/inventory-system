<?php

declare(strict_types=1);

namespace InventoryFlow\Helpers;

/**
 * Input validation helper
 */
class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate required fields
     */
    public function required(array $fields): self
    {
        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value === null || trim((string) $value) === '') {
                $this->errors[$field] = "El campo es requerido";
            }
        }

        return $this;
    }

    /**
     * Validate email format
     */
    public function email(array $fields): self
    {
        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = "Formato de email invalido";
            }
        }

        return $this;
    }

    /**
     * Validate minimum length
     */
    public function minLength(array $rules): self
    {
        foreach ($rules as $field => $length) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && strlen(trim($value)) < $length) {
                $this->errors[$field] = "Minimo {$length} caracteres";
            }
        }

        return $this;
    }

    /**
     * Validate maximum length
     */
    public function maxLength(array $rules): self
    {
        foreach ($rules as $field => $length) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && strlen(trim($value)) > $length) {
                $this->errors[$field] = "Maximo {$length} caracteres";
            }
        }

        return $this;
    }

    /**
     * Validate numeric value
     */
    public function numeric(array $fields): self
    {
        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && !is_numeric($value)) {
                $this->errors[$field] = "Debe ser un valor numerico";
            }
        }

        return $this;
    }

    /**
     * Validate positive number
     */
    public function positive(array $fields): self
    {
        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && (!is_numeric($value) || (float) $value < 0)) {
                $this->errors[$field] = "Debe ser un valor positivo";
            }
        }

        return $this;
    }

    /**
     * Validate integer
     */
    public function integer(array $fields): self
    {
        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && !ctype_digit((string) $value)) {
                $this->errors[$field] = "Debe ser un numero entero";
            }
        }

        return $this;
    }

    /**
     * Validate match between two fields
     */
    public function matches(array $rules): self
    {
        foreach ($rules as $field => $matchField) {
            $value = $this->data[$field] ?? null;
            $matchValue = $this->data[$matchField] ?? null;

            if ($value !== null && $value !== $matchValue) {
                $this->errors[$field] = "Los campos no coinciden";
            }
        }

        return $this;
    }

    /**
     * Validate in array of options
     */
    public function in(array $rules): self
    {
        foreach ($rules as $field => $options) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && !in_array($value, $options, true)) {
                $this->errors[$field] = "Valor no valido";
            }
        }

        return $this;
    }

    /**
     * Validate date format
     */
    public function date(array $fields, string $format = 'Y-m-d'): self
    {
        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value !== null) {
                $date = \DateTime::createFromFormat($format, $value);
                if (!$date || $date->format($format) !== $value) {
                    $this->errors[$field] = "Formato de fecha invalido";
                }
            }
        }

        return $this;
    }

    /**
     * Custom validation rule
     */
    public function custom(string $field, callable $callback, string $message): self
    {
        $value = $this->data[$field] ?? null;

        if ($value !== null && !$callback($value)) {
            $this->errors[$field] = $message;
        }

        return $this;
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get all errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get error for specific field
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Get first error message
     */
    public function getFirstError(): ?string
    {
        return reset($this->errors) ?: null;
    }

    /**
     * Static shorthand
     */
    public static function make(array $data): self
    {
        return new self($data);
    }

    /**
     * Validate SKU format (letters, numbers, dashes)
     */
    public function sku(array $fields): self
    {
        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && !preg_match('/^[A-Z0-9\-]+$/i', $value)) {
                $this->errors[$field] = "SKU solo puede contener letras, numeros y guiones";
            }
        }

        return $this;
    }

    /**
     * Validate phone number
     */
    public function phone(array $fields): self
    {
        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && !preg_match('/^[\d\-\+\(\)\s]{7,20}$/', $value)) {
                $this->errors[$field] = "Formato de telefono invalido";
            }
        }

        return $this;
    }

    /**
     * Validate RFC (Mexican tax ID)
     */
    public function rfc(array $fields): self
    {
        $pattern = '/^[A-Z]{3,4}\d{6}[A-Z0-9]{3}$/i';

        foreach ($fields as $field) {
            $value = $this->data[$field] ?? null;

            if ($value !== null && !preg_match($pattern, $value)) {
                $this->errors[$field] = "RFC invalido";
            }
        }

        return $this;
    }
}
