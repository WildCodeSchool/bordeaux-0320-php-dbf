<?php


namespace App\Service;

use App\Entity\Call;
use App\Entity\ContactType;
use App\Entity\RecallPeriod;

class StepForCallDataMaker
{
    public function stepMaker(Call $call)
    {
        $data = [];
        $treatments = $call->getCallProcessings();
        $callSteps = [];
        $iconsAndColors = [
            ContactType::ABANDON => [
                'icon'=> 'close',
                'color'=>'text-darken-3 grey-text'
            ],
            ContactType::CONTACT => [
                'icon'=> 'phone_in_talk',
                'color'=>'light-green-text'
            ],
            ContactType::NOT_ELIGIBLE =>[
                'icon'=> 'call_end',
                'color'=>'grey-text'
            ],
            ContactType::MSG1 => [
                'icon'=> 'perm_phone_msg',
                'color'=>'text-lighten-3 light-blue-text'
            ],
            ContactType::MSG2 => [
                'icon'=> 'perm_phone_msg',
                'color'=>'light-blue-text'
            ],
            ContactType::MSG3 => [
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
                'call_id' => $call->getId(),
                'icon'=> 'notifications_active',
                'color'=>'red-text',
            ];
        } else {
            foreach ($iconsAndColors as $iconAndColor => $values) {
                if ($callStepIdentifier === $iconAndColor) {
                    $data = [
                        'call_id' => $call->getId(),
                        'icon' => $values['icon'],
                        'color' => $values['color']
                    ];
                }
            }
        }
        return $data;
    }
}
