- I used mysql since using eloquent is much easier with laravel.
- Since this is a small service, I preferred not to create repository and service layers.
- I choose to postpone a mailjob for 5 seconds when no service can send the mail
-
    - Also I choose to postpone a mailjob for 60 seconds when an unhandled exception occured so that this time window
      provide enough time for bug fix for failing jobs.
    