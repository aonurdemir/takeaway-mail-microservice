# Environment Setup & Deployment

First of all, you should set your user and password and grant access on database in ```/mysql/init.sql```

```mysql
GRANT ALL ON laravel.* TO 'laraveluser'@'%' IDENTIFIED BY 'laravelpassword';
FLUSH PRIVILEGES;
```

After creating and granting your user and password on the db table using the file ```/mysql/init.sql```, you should set
your db and credentials like below:

```dotenv
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laraveluser
DB_PASSWORD=laravelpassword
```

Then, you should provide your mail service configurations and secrets in the .env file like below:

```dotenv
SENDGRID_API_KEY=SG.J7l2wkUPoGquw.VoqYCuKzy2jNKE7kMLg
MAILJET_KEY=188061040 
MAILJET_SECRET=4ebe2b663789f
INHOUSE_MAIL_SERVICE_URL=http://webserver/api/v1/mails
```

Then, you can set your drivers like below:

```dotenv
BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=redis
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Important

After creating your environment file, you should run below command once in the application container:

```zsh
docker-compose exec app php artisan key:generate
```

And you should run below command every time a new application code deployed:

```zsh
docker-compose exec app php artisan migrate
``` 

<b>However, the best way of doing these to run all the commands in your container image (Dockerfile).</b>

# REST API

You can find the full api specification in api-spec.yml file.

# CLI API

To send an email use below command<br>

```zsh
php artisan mail:send <from-address> <to-address> --subject=<subject> --content=<content>
```

    <from-address> should be an email address
    <to-address> should be an email address
    <subject> should be string. Can be null.
    <content> should be string. Can be null. 

# Decisions

- I used MySQL since using ```Eloquent``` is much easier with Laravel.

- To further control mails' delivery status, third party providers' callbacks can be leveraged
- To reduce high traffic coming to email services, a new batch endpoint can be exposed to provide clients to send batch
  emails.

- I chose to implement “consumer self-service” in the same git repo for simplicity and because of time restriction. The
  better way could be creating a new laravel application with separate repository. Also, in docker-compose.yml, it can
  be separated with its own ```fpm``` and ```webserver```

- I used a ```Circuit Breaker``` to prevent overkilling retries to our failed mail service. I can also implement a
  circuit breaker for ```SendGrid``` and ```Mailjet``` implementations. However, I cut that feature because of the time
  limit.

- Retry policy is handled by jobs' backoff method


