<?php

namespace App\Resources;

use Exception;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Component\HttpFoundation\Cookie;

class Access
{
    public static function login(){
        $user = 'Usuario1';
        $password = 'bpm';
        $base_uri = 'http://localhost:8080/bonita/';
        $client = HttpClient::create();

        try {
            $cookieJar = new Cookie('MiCookie', true);
            $client = ScopingHttpClient::forBaseUri($client, $base_uri, [
                'timeout' => 4.0, 
                'cookies' => $cookieJar,
                'base_uri' => $base_uri
            ]);
            $resp = $client->request('POST', 'loginservice', [
                'form_params' => [
                    'username' => $user,
                    'password' => $password,
                    'redirect' => false
                ]
                ]);
            // $client = new Client([
            //     'base_uri' => $base_uri,
            //     'timeout' => 4.0,
            //     'cookies' => $cookieJar
            // ]);
            // $resp = $client->request('POST', 'loginservice', [
            //     'form_params' => [
            //         'username' => $user,
            //         'password' => $password,
            //         'redirect' => false
            //     ]
            // ]);

            $token = $resp->getHeaders['X-Bonita-API-Token'][0];
            $_SESSION['X-Bonita-API-Token'] = $token->getValue();

            $_SESSION['user_bonita'] = $user;
            $_SESSION['password_bonita'] = $password;
            $_SESSION['base_uri_bonita'] = $base_uri;
            $_SESSION['logged'] = true;

            return $token->getValue();
        } catch (Exception $e) {
            $error = 'No se puede conectar al servidor de Bonita';
            return $error;
        }
    }
}