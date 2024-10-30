<?php 

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


define('ADVANCE_ENGINEER_SECRET_KEY', 'viskoSecret');


function encode_jwt($payload) {
    $jwt = null;
    try {
        $jwt = JWT::encode($payload, ADVANCE_ENGINEER_SECRET_KEY, 'HS256');
    } catch (\Throwable $th) {
        return $th;
    }

    return $jwt;

}

function check_header_token() {

    $header = request()->header('Authorization');

    if($header) {

        $authorization = $header->getValue();

        if(!empty($authorization)) {
            if (preg_match('/Bearer\s(\S+)/', $authorization, $token)) {
                return $token[1];
            }
        }
        
    }else{
        return false;
    }

}

function check_jwt_authentication() {

    $token = check_header_token();

    if($token) {

        $jwt = null;

        try {
            $jwt = JWT::decode($token, new Key(ADVANCE_ENGINEER_SECRET_KEY, 'HS256')) ?? "";
            return $jwt;
        } catch (\Throwable $th) {
            return false;
        }

    }else{
        return false;
    }

}