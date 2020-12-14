<?php


namespace App\Services;


use App\Exceptions\UndefinedMailService;

class MailServiceFactory
{
    public const SENDGRID = 'sendgrid';
    public const MAILJET  = 'mailjet';


    /**
     * @param string $service
     *
     * @return \App\Services\MailService
     * @throws \App\Exceptions\UndefinedMailService
     */
    public static function create(string $service): MailService
    {
        switch ($service) {
            case static::SENDGRID:
                return new MailServiceSendGridImp();
            case static::MAILJET:
                return MailServiceMailjetImp::ofVersion('v3.1');
            default:
                throw new UndefinedMailService($service);
        }
    }

}