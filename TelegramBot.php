<?php

use GuzzleHttp\Client;
ini_set('display_errors',1);


class TelegramBot
{
    protected $token = "987928654:AAHcrkwh635R8uO1keMm1yF57_WTfgXcwqQ";

    protected $updateId;

    protected function query($method, $params = [])
    {
        $url = "https://api.telegram.org/bot";

        $url .= $this->token;

        $url .= "/" . $method;

        if (!empty($params)) {
            $url .= "?" . http_build_query($params);
        }

        $client = new Client([
            'base_uri' => $url
        ]);

        $result = $client->request('GET');

        return json_decode($result->getBody());
    }

    public function getUpdates()
    {
        $response = $this->query('getUpdates', [
            'offset' => $this->updateId + 1
        ]);

        if (!empty($response) && isset($response->result[count($response->result) - 1])) {
            $this->updateId = $response->result[count($response->result) - 1]->update_id;
        }

        return $response->result;
    }

    public function sendMessage($chat_id, $text)
    {
        $response = $this->query("sendMessage", [
           'text' => $text,
            'chat_id' => $chat_id
        ]);

        return $response;
    }

}
