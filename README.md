# How to Write and Deploy a PHP Lambda Function with SAM CLI

We have three options when we need to use PHP with Lambda.

We can:

- [Package our function as a container image.](https://docs.aws.amazon.com/lambda/latest/dg/images-create.html)
- [Embed a custom PHP runtime and bootstrap file.](https://docs.aws.amazon.com/lambda/latest/dg/runtimes-custom.html)
- [Use a Lambda Layer to contain the PHP runtime and/or bootstrap.](https://docs.aws.amazon.com/lambda/latest/dg/configuration-layers.html)

This article will focus on the first option; how to build, push and deploy a PHP container image to Lambda with no 3rd party dependencies - only docker and AWS services. 

We will first explain how to structure and build the function, then show you how to test your code locally, and then show you how to deploy to AWS.

# Step 1: Install

Ensure you have installed:

- [Docker Desktop](https://docs.docker.com/desktop/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [SAM CLI](https://docs.aws.amazon.com/serverless-application-model/latest/developerguide/serverless-sam-cli-install.html)

# Step 2: Build your Function

Create your function with this structure:

```
MyApp
├── src
│   └── MyClass.php
├── 99-prod-overrides.ini
├── bootstrap
├── composer.json
├── Dockerfile
├── handler.php
├── template.yaml
```

## composer.json

We can use composer to install dependencies, define autoload standards and dump the autoload file.

[View composer.json](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/composer.json)

## bootstrap

The bootstrap file fetches the next lambda invocation and hands it to `handler.php` with the PHP CLI.

[View bootstrap](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/bootstrap)

## 99-prod-overrides.ini

We add production ini overrides with the 99-prod-overrides.ini file.  We copy this file to `/etc/php.d`.

## Dockerfile

We build from the official AWS lambda provided Amazon Linux 2 image. You can extend from any base, but you will then need to set up the Lambda runtime in your image yourself. 

We use unofficial Remi repo to make installing PHP and PHP dependencies, extensions, etc. easier. Use [this AWS tutorial](https://docs.aws.amazon.com/AWSEC2/latest/UserGuide/install-LAMP.html) if you want to go the official route.
 
Explanations are in-line:

[View Dockerfile](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/Dockerfile)

## handler.php

Create a file named handler.php in your root directory.

The handler.php file passes event data to your function code and returns a success or error response to the Lambda runtime.

In this example, `handler.php` passes event data to the `MyClass::run` static function.  It returns an error response if any exception is thrown.

[View handler.php](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/handler.php)

[View MyClass](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/tree/main/src)

## template.yaml

We use the SAM CLI to build, develop and deploy serverless cuntionss.  SAM uses [CloudFormation Templates](https://aws.amazon.com/cloudformation/resources/templates/) to define resources.

We use the `Properties.PackateType`, and `MetaData.Docker*` properties to tell SAM and AWS to 

It sets the `DEST` build arg to `aws` - you can create a parameter, or a git ignored local template file to build the local dev version.

[View template.yaml](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/template.yaml)

# Step 3: Test the System Locally

We can test the system in 2 ways:

## 1. sam local invoke ...

```
sam build
sam local invoke PhpTestFunction
```

If it works you should see:

```
Invoking Container created from phptestfunction:latest
Building image..........
Skip pulling image and use local one: phptestfunction:rapid-1.13.2.

START RequestId: xxx-xxx-xxx-xxx Version: $LATEST
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100    34  100    16  100    18  16000  18000 --:--:-- --:--:-- --:--:-- 34000
END RequestId: xxx-xxx-xxx-xxx
REPORT RequestId: xxx-xxx-xxx-xxx  Init Duration: 0.50 ms  Duration: 71.64 ms      Billed Duration: 100 ms Memory Size: 128 MB     Max Memory Used: 128 MB
Yep, it is working
```

## 2. Replicate the Lambda API Flow with cURL from host

In one terminal build and run with docker:

```
docker build --build-arg DEST=aws -t php-lambda .
docker run -p 9000:8080 -it php-lambda
```

In another terminal POST to the runtime

```
curl -XPOST "http://localhost:9000/2015-03-31/functions/function/invocations" -d "{}"
```

If it works, in the docker run terminal you should see:

```
START RequestId: d9e7cfe1-eddc-48a8-9211-a1a3e44ec3bb Version: $LATEST
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100    34  100    16  100    18   8000   9000 --:--:-- --:--:-- --:--:-- 17000
END RequestId: d9e7cfe1-eddc-48a8-9211-a1a3e44ec3bb
REPORT RequestId: d9e7cfe1-eddc-48a8-9211-a1a3e44ec3bb  Init Duration: 0.34 ms  Duration: 50.08 ms      Billed Duration: 100 ms Memory Size: 3008 MB    Max Memory Used: 3008 MB
```

In the cURL terminal you should see:

```
Yep, it is working
```

# Step 4: Deploy


# Step 5: Test the System on AWS

