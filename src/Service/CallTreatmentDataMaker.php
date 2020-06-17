<?php


namespace App\Service;

use App\Entity\Call;
use App\Entity\CallProcessing;
use App\Entity\ContactType;
use App\Entity\RecallPeriod;

class CallTreatmentDataMaker
{

    /**
     * @param CallProcessing $process
     * @return array
     */
    public static function stepMakerForProcess(CallProcessing $process)
    {
        $iconsAndColors = [
            ContactType::ABANDON => [
                'class' => 'abandon',
                'icon'  => 'close',
                'color' =>'text-darken-3 grey-text'
            ],
            ContactType::CONTACT => [
                'class' => 'contact',
                'icon'  => 'phone_in_talk',
                'color' =>'light-green-text'
            ],
            ContactType::NOT_ELIGIBLE =>[
                'class' => '',
                'icon'  => 'call_end',
                'color'  =>'grey-text'
            ],
            ContactType::MSG1 => [
                'class'=> 'message',
                'icon' => 'perm_phone_msg',
                'color'=>'amber-text'
            ],
            ContactType::MSG2 => [
                'class'=> 'message',
                'icon'=> 'perm_phone_msg',
                'color'=>'orange-text'
            ],
            ContactType::MSG3 => [
                'class'=> 'message',
                'icon'=> 'perm_phone_msg',
                'color'=>'red-text'
            ],
        ];
        $step = $process->getContactType()->getIdentifier();
        if ($step) {
            return json_decode(json_encode($iconsAndColors[$step]));
        }
    }


    /**
     * @param Call $call
     * @return array
     */
    public function stepMaker(Call $call)
    {
        $iconsAndColors = [
            ContactType::ABANDON => [
                'class' => 'abandon',
                'icon'  => 'close',
                'color' =>'text-darken-3 grey-text'
            ],
            ContactType::CONTACT => [
                'class' => 'contact',
                'icon'  => 'phone_in_talk',
                'color' =>'light-green-text'
            ],
            ContactType::NOT_ELIGIBLE =>[
                'class' => '',
                'icon'  => 'call_end',
                'color'  =>'grey-text'
            ],
            ContactType::MSG1 => [
                'class'=> 'message',
                'icon' => 'perm_phone_msg',
                'color'=>'text-lighten-3 light-blue-text'
            ],
            ContactType::MSG2 => [
                'class'=> 'message',
                'icon'=> 'perm_phone_msg',
                'color'=>'light-blue-text'
            ],
            ContactType::MSG3 => [
                'class'=> 'message',
                'icon'=> 'perm_phone_msg',
                'color'=>'text-darken-4 light-blue-text'
            ],
        ];
        $data           = [];
        $treatments     = $call->getCallProcessings();
        $callSteps      = [];

        foreach ($treatments as $step) {
            $callSteps[] = $step->getContactType()->getIdentifier();
        }
        $callStepIdentifier = end($callSteps);

        if ($call->getRecallPeriod()->getIdentifier() === RecallPeriod::URGENT) {
            $data = [
                'class' => 'emergency',
                'icon'  => 'notifications_active',
                'color' =>'red-text',
                'pulse' => 'pulse',
            ];
        } else {
            foreach ($iconsAndColors as $iconAndColor => $values) {
                if ($callStepIdentifier === $iconAndColor) {
                    $data = [
                        'class' => $values['class'],
                        'icon'  => $values['icon'],
                        'color' => $values['color']
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * @param Call $call
     * @return mixed
     */
    public function getLastTreatment(Call $call)
    {
        $treatments     = $call->getCallProcessings();
        $callSteps      = [];
        foreach ($treatments as $step) {
            $callSteps[] = $step->getContactType()->getName();
        }
        $lastStepName = end($callSteps);
        if ($call->getRecallPeriod()->getIdentifier() === RecallPeriod::URGENT) {
            $lastStepName = RecallPeriod::URGENT;
        }
        return $lastStepName;
    }
}
