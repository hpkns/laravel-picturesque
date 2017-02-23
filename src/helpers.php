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

if (!function_exists('is_json')) {
    /**
     * Test if a string contains a json object.
     *
     * @param string
     * @return bool
     */
    function is_json($str)
    {
        if (!is_string($str)) {
            return false;
        }

        json_decode($str);
        return json_last_error() == JSON_ERROR_NONE;
    }
}
