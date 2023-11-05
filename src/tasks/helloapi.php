<?php

namespace tasks;

use services\TaskApi;

require_once __DIR__ . '/../services/TaskApi.php';
$taskApi = new TaskApi();

$token = $taskApi->authGetToken('helloapi');
$task = $taskApi->getTask($token);
$answerJson = json_encode(['answer' => $task->cookie]);
$response = $taskApi->sendAnswer($token, $answerJson);
$taskApi->checkAnswerResponse($response);
