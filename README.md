# How to Write and Deploy a PHP Lambda Function with SAM CLI

We have three options when we need to use PHP with Lambda.

We can:

- [Package our function as a container image.](https://docs.aws.amazon.com/lambda/latest/dg/images-create.html)
- [Embed a custom PHP runtime and bootstrap file.](https://docs.aws.amazon.com/lambda/latest/dg/runtimes-custom.html)
- [Use a Lambda Layer to contain the PHP runtime and/or bootstrap.](https://docs.aws.amazon.com/lambda/latest/dg/configuration-layers.html)

This article will focus on the first option; how to build, push and deploy a PHP container image to Lambda.

We first explain how to structure and build the function, show you how to test your code locally, and then show you how to deploy to AWS.

# Step 0: Install and Structure

- [Docker Desktop](https://docs.docker.com/desktop/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [SAM CLI](https://docs.aws.amazon.com/serverless-application-model/latest/developerguide/serverless-sam-cli-install.html)

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

# Step 1: composer.json

We can use composer to install dependencies, define autoload standards and dump the autoload file.

[View composer.json](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/composer.json)

# Step 2: bootstrap

The bootstrap file fetches the next lambda invocation and hands it to `handler.php` with the PHP CLI.

[View bootstrap](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/bootstrap)

# Step 3: 99-prod-overrides.ini

We add production ini overrides with the 99-prod-overrides.ini file.  We copy this file to `/etc/php.d`.

# Step 3: Dockerfile

We build from the official AWS lambda provided Amazon Linux 2 image. You can extend from any base, but you will then need to set up the Lambda runtime in your image yourself. 

We use unofficial Remi repo to make installing PHP and PHP dependencies, extensions, etc. easier. Use [this AWS tutorial](https://docs.aws.amazon.com/AWSEC2/latest/UserGuide/install-LAMP.html) if you want to go the official route.
 
Explanations are in-line:

[View Dockerfile](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/Dockerfile)

# Step 4: handler.php

Create a file named handler.php in your root directory.

The handler.php file passes event data to your function code and returns a success or error response to the Lambda runtime.

In this example, `handler.php` passes event data to the `MyClass::run` static function.  It returns an error response if any exception is thrown.

[View handler.php](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/handler.php)

[View MyClass](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/tree/main/src)

# Step 5: template.yaml

We use the SAM CLI to build, develop and deploy serverless cuntionss.  SAM uses [CloudFormation Templates](https://aws.amazon.com/cloudformation/resources/templates/) to define resources.

We use the `Properties.PackateType`, and `MetaData.Docker*` properties to tell SAM and AWS to 

It sets the `DEST` build arg to `aws` - you can create a parameter, or a git ignored local template file to build the local dev version.

[View template.yaml](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/template.yaml)

# Step 6: Test the System

First build the image.

```
sam build
```

We can test the system in 2 ways:

## sam local invoke ...

Invoke 

```
sam build
```

# Step 5: samconfig.toml

