<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    protected function templateResponse($message, $status = 200, $data = [], $errors = [], $error_code = null)
    {
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ];

        if (!empty($errors)) {
            $response['errors']['code'] = $error_code;
            $response['errors']['detail'] = $errors;
        }

        return response()->json($response, $status);
}
}
