<?php


namespace App\Service;

class ClientCallbacks
{

    public static function formatCallbacksData($toProcess, $inProcess)
    {
        $data = [];
        foreach ($toProcess as $call) {
            $data['client-callback-' . $call->getId()] = $call->getClientCallback();
        }
        foreach ($inProcess as $call) {
            $data['client-callback-' . $call->getId()] = $call->getClientCallback();
        }
        return $data;
    }
}
