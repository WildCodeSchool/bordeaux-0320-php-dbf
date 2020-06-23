<?php


namespace App\Data;

class SearchData
{
    /**
     * @var string
     */
    public $phone = '';

    /**
     * @var array
     */
    public $authors = [];
    /**
     * @var string
     */
    public $subject = '';
    /**
     * @var string
     */
    public $comment = '';
    /**
     * @var bool
     */
    public $urgent = false;
    /**
     * @var string
     */
    public $clientName = '';
    /**
     * @var string
     */
    public $clientEmail = '';
    /**
     * @var string
     */
    public $immatriculation = '';
    /**
     * @var string
     */
    public $chassis = '';
    /**
     * @var string
     */
    public $city= '';
    /**
     * @var string
     */
    public $concession = '';
    /**
     * @var string
     */
    public $service = '';
    /**
     * @var integer
     */
    public $hasCome;
    /**
     * @var bool
     */
    public $isAppointmentTaken;
    /**
     * @var string
     */
    public $freeComment='';
    /**
     * @var string
     */
    public $contactType = '';
    /**
     * @var string
     */
    public $commentTransfert = '';
}
