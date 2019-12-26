<?php

namespace app\traits;

trait FormatterTrait
{
    public function customResponse($data = null, $error_message = "", $statusCode = 200)
    {
        \Yii::$app->response->statusCode = $statusCode;
        if (!$data == null) {
            if (is_object($data) && $data != null) {
                $data->error_message = $error_message;
                return $data;
            }

            if (is_array($data)){
                return array_merge($data, ['error_message' => $error_message]);
            }

        } else {
            return ['error_message' => $error_message];
        }
    }
}

