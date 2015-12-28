# VirCities Api

**vcapi** is a non-official [VirCities](http://vircities.com) API implementation for PHP 5.3+

[![Build Status](https://scrutinizer-ci.com/g/pshon/vc_api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/pshon/vc_api/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pshon/vc_api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pshon/vc_api/?branch=master)

## Getting started

1. PHP 5.3.x is required
2. Install VCAPI using [Composer](#composer-installation) (recommended) or manually
3. Install CURL library for php (if not yer installed)

## Composer Installation

1. Get [Composer](http://getcomposer.org/)
2. Require VCAPI with `php composer.phar require vircities/vcapi:dev-master` (currently not available stable release)
3. Add the following to your application's main PHP file: `require 'vendor/autoload.php';`

## Example

*Example 1* - Get user balance

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$user = new \VCAPI\Model\User();
if(!$user->Auth('login', 'password')) {
  echo "Not logged";
} else {
  echo 'My balance: ' . $user->balance . ' vd';
}
```

*Example 2* - Vacancies and work

```php
// If you want to login, use the previous example
$vacancies = new \VCAPI\Model\Vacancies();

// show all vacancies
print_r($vacancies->getList());

// show vacancies by company id
print_r($vacancies->getListByCompanyId($companyId));

// get a job
$vacancy = $vacancies->getListByCompanyId($companyId)[0];
$vacancy->getJob();

// do work of 100 energy points
$job = new \VCAPI\Model\Job();
$job->doWork(100);
```

If you need debug mode, add this code on top:

```php
\VCAPI\Common\Request::$debug = true;
```



## API

Below is a list of the public methods in the common classes you will most likely use.

```php
User::
    Auth($login, $password)             // Authorization
    getShortInfo()                      // Assign user info on current instance
    getFullInfo()                       // Return extended user information
    getCompanies()                      // Return all companies that belong to user
    UnAuth()                            // Detach user session
        
    $userId                             // Current user id
    $level                              // User exp level
    $energy                             // Current user energy
    $maxEnergy                          // Max user energy
    $balance                            // Current user balance in VD
    $health                             // Current user health
    $maxHealth                          // Max user health
    $city                               // City name
    $avatarId                           // Avatar id

Company::
    *in process...*

Corporation::
    *in process...*

Job::
    *in process...*

Worker::
    *in process...*
    
Vacancy::
    *in process...*

Vacancies::
    *in process...*

Product::
    *in process...*
    
City::
    *in process...*
```

## Avoiding responsibility
This code is shown here for informational purposes. Its use is prohibited administration [VirCities](http://vircities.com). 
Use this code you can at your own risk. The author of the code does not carry responsible for the consequences that may result from using this code.

## License

(MIT License)

Copyright (c) 2010 Chris O'Hara <cohara87@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
