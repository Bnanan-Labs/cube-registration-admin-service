#  Cubing registration

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg?style=flat)](https://php.net/)
[![codecov](https://codecov.io/gh/Bnanan-Labs/cube-registration-admin-service/branch/main/graph/badge.svg?token=1SY0TRTK4J)](https://codecov.io/gh/Bnanan-Labs/cube-registration-admin-service)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)


## About Cubing Registration
Registration system for continental sized Cubing Championships. This is the Backend running on Laravel exposing a 
Graphql API.

## Documentation
You can find the GraphQL documentation in the GraphQL playground residing on the `/graphql-playground` endpoint.

### Technologies
* **Language:** PHP `^8.1` ([PHP documentation](https://www.php.net/))
* **Framework:** Laravel ([Laravel documentation](https://laravel.com/docs/8.x))
* **API:** GraphQL ([GraphQL documentation](https://graphql.org/)) using Lighthouse ([Lighthouse Website](https://lighthouse-php.com/))
* **Test:** PHPUnit ([Laravel's test documentation](https://laravel.com/docs/8.x/testing))
* **Docker Orchestration:** Laravel Sail ([Laravel Sail documentation](https://laravel.com/docs/8.x/sail))
* **Static Code Analysis:** PHPStan ([PHPStan website](https://phpstan.org/)) through Larastan (does not support PHP 8.1 features yet 🙈)
* **Database:** MySQL
* **Queue DB:** Redis (moving to AWS SQS on production)
* **Fan-out events:** AWS SNS (if needed)


## Setup
Clone this repository
```
git clone git@github.com:Bnanan-Labs/cube-registration-admin-service.git
```

### 1-command setup
If you just want to get going, you can setup the application with
```
bash ./setup.sh
```

That's it, you should be ready to go now! 🚀

### Manual setup
Same as the setup.sh, but here you get to run the commands yourself.

Install dependencies through Docker
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

Create a local .env file
```
cp -p .env.example .env
```

Set application key
```
./vendor/bin/sail artisan key:generate
```

Starting up Sail
```
./vendor/bin/sail up -d
```

Setting up Database
```
./vendor/bin/sail artisan migrate --seed EuroSeeder
```

That's it, you should be ready to go now! 🚀

## Usage
I can highly recommend setting up following alias'
```
alias sail="./vendor/bin/sail"
alias stan="./vendor/bin/sail php ./vendor/bin/phpstan"
```

### Running the application
You can start up the application through Sail. You can decide whether you want the docker-compose to run in the 
foreground (log entries will be directed to StdOut for you to see) or let it run in the background to continue using your 
terminal
```
# runs in the foreground
sail up

# runs in the background
sail up -d
```

### Tests
You can run the full test suite with
```
sail artisan test
```

You can also specify specific tests filtered by a search string with the `--filter` option
```
sail artisan test --filter staff
```

### Migrations
You can run migrations with
```
sail artisan migrate
```

or if you want to build the database from scratch
```
sail artisan migrate:fresh
```

If you want to create a new migration use
```
sail artisan make:migration create_{table_name}_table
```

## Contribution
### Commit messages
Please use following commit message template
```
{gitmoji} {message} ({issues affected})
```

Examples
```
:broom: Cleaning up staff tests (#31)
:sparkles: Added new awesome feature! (#32 #42 #89)
:memo: Added contribution section to README.md
```

### Gitmoji
For emojis in git messages, please use this as a reference [gitmoji.dev](https://gitmoji.dev/)
