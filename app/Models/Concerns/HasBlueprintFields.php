<?php

trait HasBlueprintFields
{
    public function getFieldValue(string $path)
    {
        $parts = explode('.', $path);
        $values = $this->field_values;

        foreach ($parts as $part) {
            if (!isset($values[$part])) {
                return null;
            }
            $values = $values[$part];
        }

        return $values;
    }

    public function setFieldValue(string $path, $value): void
    {
        $parts = explode('.', $path);
        $values = &$this->field_values;

        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                $values[$part] = $value;
            } else {
                if (!isset($values[$part])) {
                    $values[$part] = [];
                }
                $values = &$values[$part];
            }
        }

        $this->save();
    }

    public function getTabFields(string $tab): array
    {
        return collect($this->field_values[$tab] ?? [])->toArray();
    }

    public function getSectionFields(string $section): array
    {
        foreach ($this->field_values as $tab => $fields) {
            if (isset($fields[$section])) {
                return $fields[$section];
            }
        }

        return $this->field_values[$section] ?? [];
    }
}
