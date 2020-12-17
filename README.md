# REST API
You can find the full api specification in api-spec.yml file.

# CLI API
To send an email use below command<br>
```php artisan mail:send <from-address> <to-address> --subject=<subject> --content=<content>```

    <from-address> should be an email address
    <to-address> should be an email address
    <subject> should be string. Can be null.
    <content> should be string. Can be null. 


# Decisions
- I used mysql since using eloquent is much easier with laravel.
- Since this is a small service, I preferred not to create repository and service layers.
- I choose to postpone a mailjob for 5 seconds when no service can send the mail
-
    - Also I choose to postpone a mailjob for 60 seconds when an unhandled exception occured so that this time window
      provide enough time for bug fix for failing jobs.

- To further control mails' delivery status, third party callbacks can be leveraged
- To reduce high traffic coming to email services, a new batch endpoint can be exposed to provide
  clients to send batch emails.
  

- Rate limiter
