<?php

use Symfony\Component\HttpFoundation\Request;

$app = require_once "../bootstrap.php";

$request = Request::createFromGlobals();

$app->handle($request);