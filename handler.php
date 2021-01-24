<?php declare(strict_types=1);

use CrazyFactory\EmailMicroService\Di\FactoryDefault;
use CrazyFactory\EmailMicroService\Exceptions\FailedMailException;
use CrazyFactory\EmailMicroService\Handler;
use CrazyFactory\EmailMicroService\Payload\Sqs\Payload;
use CrazyFactory\EmailMicroService\Setup;

require __DIR__ . '/vendor/autoload.php';

$awsLambdaRuntimeApi = $argv[1];
$requestId           = $argv[2];

try {
    (new Setup)
        ->initSentry()
        ->initDi();

    $data = json_decode($argv[3], true);

    $payload = Payload::fromData($data);

    $response = (new Handler)
        ->setPayload($payload)
        ->handle()
        ->getResponse();

    exec(<<<CMD
        curl -X POST \
        "http://${awsLambdaRuntimeApi}/2018-06-01/runtime/invocation/${requestId}/response" \
         -d "${response}"
        CMD
    );
}
catch (\Throwable $t) {
    if (!$t instanceof FailedMailException) {
        FactoryDefault::getDefault()->getSentry()->captureException($t);
    }

    $response = json_encode(
        [
            "errorMessage" => $t->getMessage(),
            "errorType"    => $t->getCode(),
        ]
    );

    exec(<<<CMD
        curl -X POST \
        "http://${awsLambdaRuntimeApi}/2018-06-01/runtime/invocation/${requestId}/error" \
         -d "${response}" \
         --header "Lambda-Runtime-Function-Error-Type: Unhandled"
        CMD
    );
}
