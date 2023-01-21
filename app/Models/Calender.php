<?php

namespace App\Models;

use DateTime;
use Exception;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Carbon\Carbon;
use Illuminate\Config\Repository;

/**
 *
 */
class Calender
{
    /**
     * @var self|null
     */
    protected static $instance = null;

    /**
     * @var
     */
    protected $client;

    protected $tz;

    protected $service = null;

    public function __construct()
    {
        /**
         * @var $config Repository
         */
        $config           = app('config');
        $clientId         = $config->get('app.spreadsheet.client_id');
        $clientEmail      = $config->get('app.spreadsheet.client_email');
        $signingKey       = $config->get('app.spreadsheet.signing_key');
        $signingAlgorithm = $config->get('app.spreadsheet.signing_algorithm');

        $this->client = new Google_Client([
            'client_id'         => $clientId,
            'client_email'      => $clientEmail,
            'signing_key'       => $signingKey,
            'signing_algorithm' => $signingAlgorithm,
        ]);
        $this->client->setScopes([
            Google_Service_Calendar::CALENDAR,
            Google_Service_Calendar::CALENDAR_EVENTS,]);
        $this->client->useApplicationDefaultCredentials();
        $this->tz = new \DateTimeZone('Asia/Tokyo');
    }

    public function getService(): Google_Service_Calendar
    {
        if (is_null($this->service)) {
            $this->service = new Google_Service_Calendar($this->client);
        }

        return $this->service;
    }

    public static function getInstance(): self
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function getEvent(string $calendarId,string $eventId)
    {
        $service = $this->getService();
        return $service->events->get($calendarId, $eventId);
    }

    /**
     * @param string $calendarId
     * @param string $date
     * @return \Google\Service\Calendar\Events
     * @throws Exception
     */
    public function search(string $calendarId,string $date = null)
    {
        $service = $this->getService();
        $date = is_null($date) ? Carbon::today() : Carbon::parse($date, $this->tz);
        $optParams = [
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => (new DateTime($date->format('Y-m-d 00:00:00')))->format(DATE_RFC3339),
            'timeMax' => (new DateTime($date->format('Y-m-d 23:59:59')))->format(DATE_RFC3339),
            'timeZone' => 'Asia/Tokyo',
        ];
        $results = $service->events->listEvents($calendarId, $optParams);
        return $results;
    }

    /**
     * @param $calendarId
     * @param $title
     * @param $start
     * @param $end
     * @return \Google\Service\Calendar\Event
     */
    public function create($calendarId, $title, $start = null, $end = null): \Google\Service\Calendar\Event
    {
        $service = $this->getService();

        $event = new Google_Service_Calendar_Event();
        $event->setStart($this->getDatetime($start));
        $event->setEnd($this->getDatetime($end));
        $event->setSummary($title);

        return $service->events->insert($calendarId, $event);
    }

    /**
     * @param string|null $datetime
     * @return Google_Service_Calendar_EventDateTime
     */
    public function getDatetime(string $datetime = null): Google_Service_Calendar_EventDateTime
    {
        $datetime = is_null($datetime) ? Carbon::today() : Carbon::parse($datetime, $this->tz);
        $googleTime = new Google_Service_Calendar_EventDateTime();
        $googleTime->setTimeZone($this->tz);
        if ($datetime->format('Hi') == '0000') {
            $googleTime->setDate($datetime->format('Y-m-d'));
        } else {
            $googleTime->setDateTime($datetime->format('c'));
        }
        return $googleTime;
    }

    /**
     * @param string $calendarId
     * @param Google_Service_Calendar_Event $event
     * @return void
     */
    public function update(string $calendarId, Google_Service_Calendar_Event $event)
    {
        $service = $this->getService();
        $eventId = $event->getId();
        $service->events->update($calendarId, $eventId, $event);
    }
}
