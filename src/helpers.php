<?php

if (!function_exists('attributes')) {
    /**
     * Parse an attributes array to as html string.
     *
     * @param  array
     * @return string
     */
    function attributes($attributes = [])
    {
        $html = [];
        foreach ((array) $attributes as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            if (is_null($value)) {
                continue;
            }

            $html[] = $key . '="' . e($value) . '"';
        }
        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }
}
