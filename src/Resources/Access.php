<?php

namespace App\Resources;

use Exception;

use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\HttpClient\HttpClient;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\ScopingHttpClient;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Session\Session;
class Access
{
    public static function login($user, $password){
        $base_uri = 'http://localhost:8080/bonita/';

        

        try {
            $cookieJar = new SessionCookieJar('MiCookie', true);
            $client = new Client([
                'base_uri' => $base_uri,
                'timeout' => 4.0,
                'cookies' => $cookieJar
            ]);
            $resp = $client->request('POST', 'loginservice', [
                'form_params' => [
                    'username' => $user,
                    'password' => $password,
                    'redirect' => false
                ]
            ]);
            
            $token = $resp;
            
            $token = $cookieJar->getCookieByName('X-Bonita-API-Token');
            $_SESSION['X-Bonita-API-Token'] = $token->getValue();
            $_SESSION['cookie'] = $cookieJar;
            $_SESSION['user_bonita'] = $user;
            $_SESSION['password_bonita'] = $password;
            $_SESSION['base_uri_bonita'] = $base_uri;
            $_SESSION['logged'] = true;
            $_SESSION['client'] = $client;

            return array('client' => $client, 'token' => $token->getValue());
        } catch (Exception $e) {
            $error = 'No se puede conectar al servidor de Bonita';
            return $error;
        }
    }
}