<?php


namespace App\Service\Landing;


class Validator
{
    /**
     * @var array
     */
    private array $errors;

    public function __construct()
    {
        $this->errors = [];
    }

    public function getIp(): string
    {
        $ip = '';
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function isValidName($name)
    {
        return ctype_alpha($name);
    }

    public function isValidImmat($name)
    {
        return preg_match("#[A-Za-z]{2,3}[-][0-9]{3}[-][A-Za-z]{2,3}# ", $name);
    }

    public function isValidDay(\DateTime $date)
    {
        $day = $date->format('N');
        return $day < 6;
    }

    public function isValidDate(\DateTime $date, $time)
    {
        $date = new \DateTime($date->format('Y-m-d') . 'T' . $time);
        $today = new \DateTime('now');
        $today->add(new \DateInterval('PT3H'));
        return $date > $today;
    }

    public function isValidPhone($phone)
    {
        $phone = str_replace(' ', '', $phone);
        return preg_match("#0[0-9]{9}# ", $phone);
    }

    public function makeSuccessMessage($form)
    {
        $m = $form->get('callMinutes')->getData() > 0 ? $form->get('callMinutes')->getData() : '';
        $message = '<span class="bolder">Merci ' . $form->get('civility')->getData()->getName() . ' ' . $form->get('name')->getData() . '</span><br>';
        $message .= 'Nous ferons notre possible pour vous rappeler le ' . $form->get('callDate')->getData()->format('d-m-Y') . ' aux alentours de ' .
            $form->get('callHour')->getData() . 'h' . $m . ' au ' . $this->formatPhone($form->get('phone')->getData());
        return $message;
    }

    private function formatPhone($phoneNumber)
    {
        return wordwrap($phoneNumber,2," ",1);
    }

}
