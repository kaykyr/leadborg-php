<?php
class LeadBorg
{
	public $TOKEN;
	public $DEBUG;
	public $DATA;

	const API_URL      = "https://api.leadb.org:9090";
	const SEEN         = "seen";
	const SEND_MESSAGE = "sendmessage";
	const SEND_MEDIA   = "sendmedia";
	const GET_UNREAD   = "getunread";
	const GET_ALL      = "getall";
	const GET_HISTORY  = "gethistory";
	const PHONE_DOMAIN = "@c.us";
	const GROUP_DOMAIN = "@g.us";

	public function __construct(string $token, bool $debug = false)
	{
		$this->TOKEN = $token;
		$this->DEBUG = $debug;
		return $this;
	}

	public function createUrl(string $method, array $args = [])
	{
		$args['token'] = $this->TOKEN;
		return self::API_URL . '/?cmd=' . $method . '&' . http_build_query($args);
	}

	public function query(string $method, array $args)
	{
		$url = $this->createUrl($method, $args);

		if ($this->DEBUG) {
			print("[DEBUG]: \n");
			print("Requested URL: " . $url . "\n");
		}

		return file_get_contents($url);
	}

	public function sendMessage(string $chatId, string $message)
	{
		if (strpos($chatId, '-') !== false)
			$chatId .= self::GROUP_DOMAIN;
		else
			$chatId .= self::PHONE_DOMAIN;

		$response = json_decode($this->query(self::SEND_MESSAGE, ['chatId' => $chatId, 'content' => $message]));

		if ($this->DEBUG) {
			print("[DEBUG]: \n");
			var_dump($response);
		}

		if ($response->status == "OK")
			return true;
		else
			return false;
	}

	public function sendMedia(string $chatId, string $path, string $caption)
	{
		if (strpos($chatId, '-') !== false)
			$chatId .= self::GROUP_DOMAIN;
		else
			$chatId .= self::PHONE_DOMAIN;

		$response = json_decode($this->query(self::SEND_MEDIA, ['chatId' => $chatId, 'filePath' => $path, 'caption' => $caption]));

		if ($this->DEBUG) {
			print("[DEBUG]: \n");
			var_dump($response);
		}

		if ($response->status == "OK")
			return true;
		else
			return false;
	}

	public function sendSeen(string $chatId)
	{
		if (strpos($chatId, '-') !== false)
			$chatId .= self::GROUP_DOMAIN;
		else
			$chatId .= self::PHONE_DOMAIN;

		$response = json_decode($this->query(self::SEEN, ['chatId' => $chatId]));

		if ($this->DEBUG) {
			print("[DEBUG]: \n");
			var_dump($response);
		}

		if ($response->status == "OK")
			return true;
		else
			return false;
	}

	public function onReceiveMessage($callback)
	{
		if ($_GET['token'] == $this->TOKEN) {
			$data = json_decode(base64_decode($_GET['data']));
			self::sendSeen($data->chatId->user);
			$callback($this, $data);
		}
	}
}
