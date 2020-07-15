<?php


namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\HeadBoardData;

class HeadBoardDataTest extends testCase
{
    private $data = [
        0 => [
            "city" => "Bordeaux",
            "concession" => "La Teste",
            "service_id" => "444",
            "service" => "Carrosserie",
            "toprocess" => "2",
            "inprocess" => "1",
        ],
        1 => [
            "city" => "Bordeaux",
            "concession" => "La Teste",
            "service_id" => "445",
            "service" => "Financement",
            "toprocess" => "4",
            "inprocess" => "1",
        ],
        2 => [
            "city" => "Bordeaux",
            "concession" => "La Teste",
            "service_id" => "446",
            "service" => "Pièces détachées",
            "toprocess" => "5",
            "inprocess" => "6",
        ]
    ];

    public function makeDataForHeadTest()
    {
    }
}
