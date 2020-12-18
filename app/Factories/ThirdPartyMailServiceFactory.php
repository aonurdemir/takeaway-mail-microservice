<?php


namespace App\Factories;


use App\Exceptions\UndefinedMailService;
use App\Services\MailjetMailProvider;
use App\Services\SendGridMailProvider;
use App\Services\MailProvider;

class ThirdPartyMailServiceFactory
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
     * @return \App\Services\MailProvider
     * @throws \App\Exceptions\UndefinedMailService
     */
    public static function create(string $service): MailProvider
    {
        switch ($service) {
            case static::SENDGRID:
                return SendGridMailProvider::create();
            case static::MAILJET:
                return MailjetMailProvider::ofVersion('v3.1');
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