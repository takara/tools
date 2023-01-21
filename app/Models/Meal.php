<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;

class Meal
{
    protected static $instance = null;

    protected $calendarId;

    /**
     * @var Event|null
     */
    protected $lastEvent = null;

    /**
     * @return self
     */
    public static function getInstance(): ?Meal
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * @param string $calendarId
     * @return void
     * @throws Exception
     */
    public function execute(string $calendarId)
    {
        $this->calendarId = $calendarId;
        $calendar = Calender::getInstance();

        $lastEvent = $this->getLastEvent();

        $now = Carbon::now()->format('Y/m/d H:i');
        $nextSummary = $this->getNextSummary();
        if ($this->isExpired()) {
            Log::debug('[meal]完了し忘れ');
            $this->closeLastEvent();
        }
        if (is_null($this->lastEvent)) {
            Log::debug("[meal]新規作成[$nextSummary]");
            $calendar->create($calendarId, $nextSummary, $now, $now);
        } else {
            Log::debug('[meal]時間変更完了');
            $lastEvent->setEnd($calendar->getDatetime($now));
            $calendar->update($calendarId, $lastEvent);
        }
    }

    /**
     * @return void
     */
    protected function closeLastEvent()
    {
        $calendar = Calender::getInstance();
        $start = $this->lastEvent->getStart();
        $end = Carbon::parse($start->getDateTime());
        $end->addMinutes(10);
        $this->lastEvent->setEnd($calendar->getDatetime($end->format('Y/m/d H:i')));
        $calendar->update($this->calendarId, $this->lastEvent);
        $this->lastEvent = null;
    }

    /**
     * @return bool
     */
    protected function isExpired(): bool
    {
        $ret = false;
        if (is_null($this->lastEvent)) {
            return false;
        }
        $now = Carbon::now()->timestamp;
        $start = $this->lastEvent->getStart()->getDateTime();
        $end = $this->lastEvent->getEnd()->getDateTime();
        if ($start == $end) {
            Log::debug('[meal]開始のみ');
            $start = strtotime($start);
            // 1時間以上経ってたら終了し忘れ
            if ($now - $start > 3600) {
                $ret = true;
            }
        } else {
            Log::debug('[meal]前の食事');
            $this->lastEvent = null;
        }
        return $ret;
    }

    /**
     * @return string
     */
    protected function getNextSummary(): string
    {
        if (is_null($this->lastEvent)) {
            $nextSummary = "朝食";
        } else {
            $nextSummaryList = [
                '朝食' => '昼食',
                '昼食' => '夕食',
            ];
            $summary = $this->lastEvent->summary;
            $nextSummary = $nextSummaryList[$summary] ?? '食事';
        }
        return $nextSummary;
    }

    /**
     * @return Event|null
     * @throws Exception
     */
    protected function getLastEvent(): ?Event
    {
        $calendar = Calender::getInstance();
        $calendarId = $this->calendarId;
        $events = $calendar->search($calendarId);
        $this->lastEvent = null;
        /** @var Event $event */
        foreach ($events as $event) {
            if (in_array($event->summary, ['朝食', '昼食', '夕食', '食事'])) {
                $this->lastEvent = $event;
            }
        }
        return $this->lastEvent;
    }
}
