<?php


namespace App\Service;

use App\Entity\Call;
use App\Entity\ContactType;
use App\Entity\RecallPeriod;

class StepForCallDataMaker
{
    /**
     * @param Call $call
     * @return array
     */
    public function stepMaker(Call $call)
    {
        $data           = [];
        $treatments     = $call->getCallProcessings();
        $callSteps      = [];
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
}
