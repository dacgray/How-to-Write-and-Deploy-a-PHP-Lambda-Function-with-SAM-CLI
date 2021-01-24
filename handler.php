<?php declare(strict_types=1);


use My\App\MyClass;

require __DIR__ . '/vendor/autoload.php';

$awsLambdaRuntimeApi = $argv[1];
$requestId           = $argv[2];

try {
    $data = json_decode($argv[3], true);

    $response = MyClass::run($data);

    // Return a successful response to the Lambda runtime
    exec(<<<CMD
        curl -X POST \
        "http://${awsLambdaRuntimeApi}/2018-06-01/runtime/invocation/${requestId}/response" \
         -d "${response}"
        CMD
    );
}
// Catch any errors and return an error response to the Lambda runtime
catch (\Throwable $t) {
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
