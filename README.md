# REST API

You can find the full api specification in api-spec.yml file.

# CLI API

To send an email use below command<br>
```php artisan mail:send <from-address> <to-address> --subject=<subject> --content=<content>```

    <from-address> should be an email address
    <to-address> should be an email address
    <subject> should be string. Can be null.
    <content> should be string. Can be null. 
# Env
- 
  SENDGRID_API_KEY=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
  MAILJET_KEY=18c993dff869a49922a8a28a48061040
  MAILJET_SECRET=4ebe2b659e7b0825e61e6b992263789f
  INHOUSE_MAIL_SERVICE_URL=http://webserver/api/v1/mails

# Decisions

- I used mysql since using eloquent is much easier with laravel.

- To further control mails' delivery status, third party callbacks can be leveraged
- To reduce high traffic coming to email services, a new batch endpoint can be exposed to provide clients to send batch
  emails.

- I choose to implement “consumer self-service” in the same git repo for simplicity and because of time restriction. The
  better way could be creating a new laravel application with separate repository.
  

- Retry policy is handled by jobs' backoff method


