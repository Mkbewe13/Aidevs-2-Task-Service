<?php

namespace services;

use CurlHandle;
use Exception;
use stdClass;

class TaskApi
{
    const TASKS_TOKEN_URL_BASE = 'https://zadania.aidevs.pl/token/';
    const TASKS_GET_URL_BASE = 'https://zadania.aidevs.pl/task/';
    const TASKS_ANSWER_URL_BASE = 'https://zadania.aidevs.pl/answer/';


    private CurlHandle $curlHandle;

    public function __construct()
    {
        $this->curlHandle = curl_init();
    }


    /**
     * @throws Exception
     */
    public function authGetToken(string $taskName): ?string
    {
        $response = $this->postRequest(self::TASKS_TOKEN_URL_BASE . $taskName, $this->getApiKeyJson());
        $response = json_decode($response);
        return $response->token ?? null;
    }

    /**
     * @throws Exception
     */
    public function getTask(string $taskToken): stdClass
    {
        $response = $this->getRequest(self::TASKS_GET_URL_BASE . $taskToken);
        return json_decode($response);
    }

    public function sendAnswer(string $taskToken, string $answerJson): stdClass
    {
        $response = $this->postRequest(self::TASKS_ANSWER_URL_BASE . $taskToken, $answerJson);
        return json_decode($response);
    }


    private function postRequest(string $url, string $data): bool|string
    {
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);

        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_POST, 1);
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($this->curlHandle);

        curl_close($this->curlHandle);
        return $response;
    }

    /**
     * @throws Exception
     */
    private function getRequest($url): bool|string
    {
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);

        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_HTTPGET, true);


        $response = curl_exec($this->curlHandle);

        curl_close($this->curlHandle);


        return $response;
    }

    private function getApiKeyJson(): false|string
    {
        return json_encode(['apikey' => $_ENV["AIDEVS_TASK_API_KEY"]]);
    }

    public function prepareAnswer($answer): false|string
    {
        $result = ['answer' => $answer];
        return json_encode($result);
    }

    public function checkAnswerResponse(stdClass $response): void
    {
        if ($response->code === 0) {
            echo "SUKCES! :) \n Oto odpowiedź z API na rozwiązanie: \n";
            print_r($response);
        } else {
            echo "NIESTETY.. coś poszło nie tak. \n Oto odpowiedź z API na rozwiązanie: \n";
            print_r($response);
        }
    }


}
