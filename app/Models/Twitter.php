<?php

namespace App\Models;

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{
	protected $consumer_key = null;
	protected $consumer_key_sercret = null;
	protected $access_token = null;
	protected $access_token_secret = null;
	protected $connection = null;

	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
		$this->consumer_key         = app('config')->get('app.consumer_key');
		$this->consumer_key_sercret = app('config')->get('app.consumer_key_sercret');
		$this->access_token         = app('config')->get('app.access_token');
		$this->access_token_secret  = app('config')->get('app.access_token_secret');

		$cur = "";
		$this->connection = new TwitterOAuth(
			$this->consumer_key,
			$this->consumer_key_sercret,
			$this->access_token,
			$this->access_token_secret
		);
	}

	/**
	 * ユーザー情報取得
	 */
	public function getUserInfo(string $id, array $opt = [])
	{
		$param = ['user_id' => $id];
		$res = $this->connection->get("users/lookup", $param);
		return $res;
	}

	/**
	 * ユーザーの良いね取得
	 */
	public function getFavorites(string $screenName, array $opt = [])
	{
		$count = $opt['count'] ?? 10;
		$param = ['screen_name' => $screenName, 'count' => $count];
		$res = $this->connection->get("favorites/list", $param);
		foreach ($res as $data) {
			$ret[] = $data->id;
		}
		return $ret;
	}

	/**
	 * 記事反応ユーザー取得
	 */
	public function getRepryUsers(string $id)
	{
		$ret = array_unique(array_merge(
			$this->getLikingUsers($id),
			$this->getRetweet($id)
		));
		return $ret;
	}

	/**
	 * 記事いいねユーザー取得
	 * @todo 正攻法になおす(v2)
	 */
	public function getLikingUsers(string $id)
	{
        $client = new \Google_Client();

        $param = [];
		$res = $this->connection->setApiVersion("2");
		$res = $this->connection->get("tweets/{$id}/liking_users", $param);
		$ret = [];
		foreach ($res->data as $data) {
			$ret[] = $data->username;
		}
		return $ret;
	}

	/**
	 * 記事リツイートユーザー取得
	 */
	public function getRetweet(string $id)
	{
		$param = ['id' => $id];
		$res = $this->connection->get("statuses/retweeters/ids", $param);
		$ret = [];
		foreach ($res->ids as $id) {
			print "$id\n";
			$user = $this->getUserInfo($id);
			$ret[] = $user[0]->screen_name;
		}
		return $ret;
	}

	/**
	 * 指定ユーザータイムライン取得
	 */
	public function getUserTimeline(string $screenName, array $opt = []) : array
	{
		$count = $opt['count'] ?? 10;
		$excludeReplies= $opt['exclude_replies'] ?? false;
		$ret = [];
		$param = ['screen_name' => $screenName, 'count' => $count, 'exclude_replies' => $excludeReplies];
		$res = $this->connection->get('statuses/user_timeline', $param);
		foreach ($res as $tweet) {
			if (isset($tweet->entities->media) === false) {
				continue;
			}
			if (isset($tweet->entities->hashtags) === false) {
				continue;
			}
			$ret[] = [
				'id'             => $tweet->id,
				'text'           => $tweet->text,
				'retweet_count'  => $tweet->retweet_count,
				'favorite_count' => $tweet->favorite_count
			];
		}
		return $ret;
	}

	/**
	 * ツイート検索
	 */
	public function search(string $keyword, $count = 10)
	{
		$param = ['q' => $keyword, 'count' => $count, 'result_type' => 'recent'];
		$res = $this->connection->get('search/tweets', $param);
		return $res;
	}

	/**
	 * フォローユーザー取得
	 * 短時間で何回もやると制限かかる
	 */
	public function getFriends(string $screenName) : array
	{
		$ret = [];
		do {
			$param = ['screen_name' => $screenName, 'count' => 200];
			if (!empty($cur)) {
				$param += ['cursor' => $cur];
			}
			$res = $this->connection->get('friends/list', $param);
			if (!isset($res->users)) {
				print_r($res);
				throw new Exception(1, "error");
			}
			foreach ($res->users as $user) {
				$ret[] = [
					'name'        => $user->name,
					'screen_name' => $user->screen_name,
				];
			}
			$cur=$res->next_cursor;
		} while ($res->next_cursor > 0);
		return $ret;
	}

	public function getFolloers(string $screenName, array $opt = [])
	{
		$cur = 0;
		$ret = [];
		do {
			$param = ['screen_name' => $screenName, 'count' => 200];
			if (!empty($cur)) {
				$param += ['cursor' => $cur];
			}
			$param = ['screen_name' => $screenName, 'count' => 200];
			$res = $this->connection->get('followers/list', $param);
			if (!isset($res->users)) {
				print_r($res);
				throw new Exception(1, "error");
			}
			foreach ($res->users as $user) {
				$ret[] = [
					'name'        => $user->name,
					'screen_name' => $user->screen_name,
				];
			}
			print $res->next_cursor_str."\n";
			if ($cur == $res->next_cursor_str) {
				break;
			}
			$cur = $res->next_cursor;
		} while ($res->next_cursor > 0);
		return $ret;
	}
}
