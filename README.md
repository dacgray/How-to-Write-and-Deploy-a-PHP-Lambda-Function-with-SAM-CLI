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

Note:
- the bootstrap fetches the next lambda invocation and hands it to handler.php with the PHP CLI

[View bootstrap](https://github.com/dacgray/How-to-Write-and-Deploy-a-PHP-Lambda-Function-with-SAM-CLI/blob/main/bootstrap)

# Step 2: Dockerfile

Create a file named Dockerfile in your root directory.

Note:

- this Dockerfile builds from the official AWS lambda provided Amazon Linux 2 image. You can extend from any base, but you will then need to set up the Lambda runtime. This is the easier option. Available packages on the Enterprise Linux official repo are limited. The unofficial Remi repo makes installing PHP and PHP dependencies, extensions, etc. easier. Use this AWS tutorial if you instead want to go the official route.
- We can create 2 images from a single Dockerfile. This makes development and deployment more maintainable.

```
$var = 123;
```
