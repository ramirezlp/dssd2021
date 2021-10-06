<?php

namespace App\Resources;

use Symfony\Component\HttpClient\HttpClient;

class Process
{
    public static function intiateProcess($id){
        $client = HttpClient::create();
        $response = $client->request('POST', 'API/bpm/process'.$id.'instantiation');
        return $response;
    }

    public static function getProcessId($processName){
        $$response = Request::doTheRequest('GET', 'API/bpm/process?s='.$processName['data'->id]);
        return $response;
    }

    public static function setVariable($taskId, $variable, $valor, $tipo){
        $taskId = Request::doTheRequest('GET', 'API/bpm/userTask/'.$taskId);
        $response = Request::doTheRequest('PUT', 'API/bpm/caseVariable/'.$taskId['data']->caseId.'/'.$variable, $variable, $valor, $tipo);
        return $response;
    }

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