# javanile/vendor-producer

[![Build Status](https://travis-ci.org/javanile-bot/producer.svg?branch=master)](https://travis-ci.org/javanile-bot/producer)
[![StyleCI](https://styleci.io/repos/82387350/shield?branch=master)](https://styleci.io/repos/82387350)
[![Code Climate](https://codeclimate.com/github/javanile-bot/producer/badges/gpa.svg)](https://codeclimate.com/github/javanile-bot/producer)
[![Test Coverage](https://codeclimate.com/github/javanile-bot/producer/badges/coverage.svg)](https://codeclimate.com/github/javanile-bot/producer/coverage)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8a8268b9-2798-4ca3-8515-79042d809105/mini.png)](https://insight.sensiolabs.com/projects/8a8268b9-2798-4ca3-8515-79042d809105)

Produce your vendor code everywhere

## Install Producer

Install via composer copy and paste the follow commands into console

```bash
composer require javanile/vendor-producer --dev
```

## Working with Producer

Producer provide to different macro/task ready for development process

### Init repository

You can init a repository and mount it as vendor code

```bash
$ php producer init <repository-url>
```

### Clone repository

You can clone a repository and mount it as vendor code, 
but you continue to develop it without 
switch between diffent projects

```bash
$ php producer clone <repository-url>
```
