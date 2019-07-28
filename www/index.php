<?php

require __DIR__.'/../vendor/autoload.php';

use AppVal\App;

$app = new App();
echo $app->handeRequest();
exit;