<?php


namespace App\Services;


use App\Exceptions\UndefinedMailService;

class MailServiceFactory
{
    public const SENDGRID = 'sendgrid';
    public const MAILJET  = 'mailjet';

    private const SERVICE_NAMES = [
        self::SENDGRID,
        self::MAILJET,
    ];


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
                return MailServiceSendGridImp::create();
            case static::MAILJET:
                return MailServiceMailjetImp::ofVersion('v3.1');
            default:
                throw new UndefinedMailService($service);
        }
    }


    /**
     * @return array
     * @throws \App\Exceptions\UndefinedMailService
     */
    public static function createAllServices(): array
    {
        $services = [];
        foreach (self::SERVICE_NAMES as $serviceName) {
            array_push($services, static::create($serviceName));
        }

        return $services;
    }

}