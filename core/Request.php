<?php

namespace app\core;

class Request
{
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === 'get';
    }

    public function isPost()
    {
        return $this->method() === 'post';
    }

    public function getBody()
    {
        // Empty associative array
        $body = [];
        if ($this->method() === 'get') {
            // $_GET: A PHP superglobal that holds query parameters from the URL (e.g., ?key=value).
            // Iterates through each key-value pair in the $_GET array
            foreach ($_GET as $key => $value) {
                // Sanitizes the value associated with $key from $_GET
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === 'post') {
            // Check Content-Type header to determine how to process the body
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (strpos($contentType, 'application/json') !== false) {
                // Handle JSON input
                $rawInput = file_get_contents('php://input');
                $decodedInput = json_decode($rawInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedInput)) {
                    $body = $decodedInput;
                }
            } else {
                // $_POST: A PHP superglobal that holds form data submitted via HTTP POST.
                // Iterates through each key-value pair in the $_POST array
                // Handle form-encoded input
                foreach ($_POST as $key => $value) {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
        // Returns the sanitized data
        return $body;
    }
}
