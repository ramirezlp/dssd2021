<?php

namespace App\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpClient\HttpClient;

class Process
{
    public static $baseUri = "http://localhost:8080/bonita/API/bpm/";

    public static function get($url)
    {
        $response = $_SESSION['client']->get(
            Process::$baseUri . $url,
            [
                'cookies' => $_SESSION['cookie']
            ]
        );

        return $response->getBody();
    }

    public static function put($url, $data)
    {
        $response = $_SESSION['client']->put(
            Process::$baseUri . $url,
            [
                'json' => $data,
                'cookies' => $_SESSION['cookie'],
                'headers' => [
                  'X-Bonita-API-Token' => $_SESSION['X-Bonita-API-Token']
                ]
            ]
        );
        return $response->getBody();
    }

    public static function post($url, $data)
    {
        $response = $_SESSION['client']->post(
            Process::$baseUri . $url,
            [
                'cookies' => $_SESSION['cookie'],
                'headers' => [
                  'X-Bonita-API-Token' => $_SESSION['X-Bonita-API-Token']
                ]
            ]
        );
        return $response->getBody();
    }

    public static function initializeProcess($client, $id){
        $url = 'process/'.$id.'/instantiation';
        $resp = Process::post($url, []);
        $resp = $resp->getContents();
        $json = json_decode($resp);
        return $json->caseId;
    }

    public static function getProcessId($client, $processName){

        $resp = Process::get('process?s=' . $processName);
        $json = json_decode($resp->getContents())[0];

        return $json->id;
    }

    public static function setVariableByCase($caseId, $variable, $valor, $tipo){
        if($valor === null){
            return false;
        }
        $json = [
            "type" => $tipo,
            "value" => $valor
        ];
        $response = Process::put('caseVariable/'.$caseId.'/'.$variable , 
                $json);
        return $response;
    }

    public static function getIdByUsername($username){
        $response = $_SESSION['client']->get(
            "http://localhost:8080/bonita/API/identity/user?f=userName=" . $username,
            [
                'cookies' => $_SESSION['cookie']
            ]
        );
        $json = json_decode($response->getBody()->getContents())[0];
        return $json->id;
    }

    public static function getHumanTaskByCase($caseId, $taskName){
        $response = Process::get('humanTask?f=name=' . $taskName . '&f=caseId=' . $caseId);
        $content = json_decode($response->getContents());
        $json = $content[0];
        return $json->id;
    }
    

    public static function assignTask($taskId, $userId){
        if($userId == null){
            return false;
        }
        $json = [
            "assigned_id" => $userId,
        ];
        $response = Process::put('humanTask/'.$taskId , 
                $json)->getContents();
        return $response;
    }

    /*public static function searchActivityByCase($caseId){
        $response = Request::doTheRequest('GET', 'API/bpm/task?f=caseId='.$caseId);
        return $response;
    }*/

    //SOLAMENTE FUNCIONA PARA USERTASK PERO NOSOTROS EN EL REGISTRO DEL FORM USAMOS HUMANTASK
    public static function completeActivity($taskId){

        $resp = Process::post("userTask/" . $taskId . "/execution", []);
        $json = json_decode($resp->getContents());

        return $json;
    }
}