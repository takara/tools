<?php

namespace App\Models;

use GuzzleHttp\Client;

class SwitchBotAPI
{
	protected static $instance = null;
	const ACCESS_POINT = "https://api.switch-bot.com/v1.0/";
	public static function getInstance() : self
	{
		if (is_null(static::$instance)) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	public function getStatus(string $deviceId) : array
	{
		$token = $this->getToken();
		$client = new Client([
			'base_uri' => self::ACCESS_POINT,
		]);
		$res = $client->request('GET', "devices/{$deviceId}/status", [
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => $token,
			]
		]);
		$json = $res->getBody();
		return json_decode($json, true);
	}

	public function commands(string $deviceId, $json) : array
	{
		$token = $this->getToken();
		$client = new Client([
			'base_uri' => self::ACCESS_POINT,
		]);
		$res = $client->request('POST', "devices/{$deviceId}/commands", [
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => $token,
			],
			'body' => $json,
		]);
		$json = $res->getBody();
		return json_decode($json, true);
	}

	public function getDevices() : array
	{
		$token = $this->getToken();
		$client = new Client([
			'base_uri' => self::ACCESS_POINT,
		]);
		$res = $client->request('GET', "devices", [
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => $token,
			]
		]);
		$json = $res->getBody();
		return json_decode($json, true);
	}

	protected function getToken() : string
	{
		return app("config")->get("app.switchbot.token");
	}
}
