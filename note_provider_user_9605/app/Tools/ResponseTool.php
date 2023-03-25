<?php

namespace App\Tools;

use App\Constants\ResponseCode;

class ResponseTool
{
    public static function success(array $data = [])
    {
        return self::commonResuls(ResponseCode::SUCCESS, ResponseCode::getMessage(ResponseCode::SUCCESS), $data);
    }

    public static function error(int $code = ResponseCode::ERROR, string $message = '', array $data = [])
    {
        if (empty($message)) {
            return self::commonResuls($code, ResponseCode::getMessage($code), $data);
        } else {
            return  self::commonResuls($code, $message, $data);
        }
    }

    // 返回统一的数据
    public static function commonResuls(int $code, string $message, array $data)
    {
        return [
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ];
    }


}