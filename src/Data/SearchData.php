<?php


namespace App\Data;

class SearchData
{
    /**
     * @var string
     */
    public $phone = '';

    /**
     * @var string
     */
    public $author = '';
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
    public $isUrgent = false;
    /**
     * @var string
     */
    public $name = '';
    /**
     * @var string
     */
    public $email = '';
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
    public $town= '';
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
    public $commentTransfer = '';
    private $dateFrom;
    private $dateTo;


    /**
     * @return mixed
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param mixed $dateFrom
     */
    public function setDateFrom($dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return mixed
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param mixed $dateTo
     */
    public function setDateTo($dateTo): void
    {
        $this->dateTo = $dateTo;
    }



}
