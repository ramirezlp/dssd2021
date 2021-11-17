<?php

namespace App\Resources;

use CURLFile;

use Exception;
use GuzzleHttp\Client;

use GuzzleHttp\Cookie\SessionCookieJar;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Component\HttpFoundation\Session\Session;

class ApiEstampillado
{
    public static $loginUri = "https://api-estampillado-dssd.herokuapp.com/authenticate";
    public static $estampilladoUri = "https://api-estampillado-dssd.herokuapp.com/estampillado/estampillar";
    public static $qrUri = "https://neutrinoapi-qr-code.p.rapidapi.com/qr-code";
    public static $qrToken = "1681ee10b8msh1f3f353d870294dp191971jsnc1b259fffde6";


    public static function login($username, $password){

        try {
            $ch = curl_init( ApiEstampillado::$loginUri);
            $payload = json_encode( array( "username"=> $username, "password" => $password ) );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result);
            $_SESSION['herokuToken'] = $result->token;

            return $result->token;

        } catch (Exception $e) {
            $error = 'No se puede conectar al servidor de Estampillado';
            return $error;
        }
    }

    public static function estampillar($token, $expediente, $archivo){

        try {
            $fileName = $_SERVER["DOCUMENT_ROOT"]."uploads/" . $archivo;
            $fileSize = filesize($fileName);
            
            if(!file_exists($fileName)) {
                echo "No existe el archivo";die;
            }
            
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $finfo = finfo_file($finfo, $fileName);
            
            $cFile = new CURLFile($fileName, $finfo, basename($fileName));
            $data = array( "estatuto" => $cFile, "nuExpediente" => $expediente);
            
            $cURL = curl_init(ApiEstampillado::$estampilladoUri);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            
            // This is not mandatory, but is a good practice.
            curl_setopt($cURL, CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: multipart/form-data',
                    "Authorization: Bearer " . $token
                )
            );
            curl_setopt($cURL, CURLOPT_POST, true);
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $data);
            curl_setopt($cURL, CURLOPT_INFILESIZE, $fileSize);
            
            $response = curl_exec($cURL);
            curl_close($cURL);

            return $response;

        } catch (Exception $e) {
            $error = 'No se puede conectar al servidor de Estampillado';
            return $error;
        }
    }

    public static function getQr($hash){

        try {

            $data = array( "content" => "http://localhost:8000/verSAQR/?hash=" . $hash);
            
            $cURL = curl_init(ApiEstampillado::$qrUri);
            
            // This is not mandatory, but is a good practice.
            curl_setopt($cURL, CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'x-rapidapi-host: neutrinoapi-qr-code.p.rapidapi.com',
                    'x-rapidapi-key: ' . ApiEstampillado::$qrToken
                )
            );
            curl_setopt($cURL, CURLOPT_POST, 1);
            curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($cURL, CURLOPT_BINARYTRANSFER,1);
            $response = curl_exec($cURL);
            curl_close($cURL);
            

            return $response;

        } catch (Exception $e) {
            $error = 'No se puede conectar al servidor de Estampillado';
            return $error;
        }
    }


}