# How to Write and Deploy a PHP Lambda Function with SAM CLI

We have three options when we need to use PHP with Lambda.

We can:

- Build, push and deploy a container image.
- Embed a custom PHP runtime and bootstrap file.
- Use a Lambda Layer with the PHP runtime and/or bootstrap.

This article will focus on the first option; how to build, push and deploy a PHP container image to Lambda.

# Step 0: Install

- [Docker Desktop](https://docs.docker.com/desktop/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [SAM CLI](https://docs.aws.amazon.com/serverless-application-model/latest/developerguide/serverless-sam-cli-install.html)

# Step 1: bootstrap

Create a file named bootstrap in your root directory.

The bootstrap file fetches the next lambda invocation and hands it to `handler.php` with the PHP CLI.

[View bootstrap](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/bootstrap)

# Step 2: Dockerfile

Create a file named Dockerfile in your root directory.

Note:

- We build from the official AWS lambda provided Amazon Linux 2 image. You can extend from any base, but you will then need to set up the Lambda runtime in your image yourself. 
- We use unofficial Remi repo to make istalling PHP and PHP dependencies, extensions, etc. easier. Use [this AWS tutorial](https://docs.aws.amazon.com/AWSEC2/latest/UserGuide/install-LAMP.html) if you want to go the official route.

Explanations are in-line:

[View Dockerfile](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/Dockerfile)

# Step 3: handler.php

Create a file named handler.php in your root directory.

The handler.php file handles event data and returns a success or error response to the Lambda runtime.

[View handler.php](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/handler.php)

