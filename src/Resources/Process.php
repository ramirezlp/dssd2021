<?php

namespace App\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpClient\HttpClient;

class Process
{

    public static function initializeProcess($client, $id){
        $resp = $client->post('http://localhost:8080/bonita/API/bpm/process/'.$id.'/instantiation');
        $json = json_decode($resp->getBody()->getContents())[0];
        return $json->caseId;
    }

    public static function getProcessId($client, $processName){

        $resp = $client->get('http://localhost:8080/bonita/API/bpm/process?s=' . $processName);
        $json = json_decode($resp->getBody()->getContents())[0];

        return $json->id;
    }

    /*
    public static function setVariable($taskId, $variable, $valor, $tipo){
        $taskId = Request::doTheRequest('GET', 'API/bpm/userTask/'.$taskId);
        $response = Request::doTheRequest('PUT', 'API/bpm/caseVariable/'.$taskId['data']->caseId.'/'.$variable, $variable, $valor, $tipo);
        return $response;
    }*/

    public static function setVariableByCase($caseId, $variable, $valor, $tipo){
        $response = Request::doTheRequest('PUT', 'API/bom/caseVariable/'.$caseId.'/'.$variable, $variable, $valor, $tipo);
        return $response;
    }

    public static function assignTask($taskId, $userId){
        $response = Request::doTheRequest('PUT', 'API/bom/userTask/'.$taskId, null, $userId, null, true);
        return $response;
    }

    public static function searchActivityByCase($caseId){
        $response = Request::doTheRequest('GET', 'API/bpm/task?f=caseId='.$caseId);
        return $response;
    }

    public static function completeActivity($taskId){
        $response = Request::doTheRequest('GET', 'API/bpm/userTask/'.$taskId.'/execution');
        return $response;
    }

    // public static function getVariable($taskId, $variable){
    //     $
    // }

    // public static function setVariable($taskId, $variable, $valor, $tipo){
        
    // }
}