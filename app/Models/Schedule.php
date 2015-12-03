<?php

namespace App\Models;

use App\Models\ScheduleItem;

class Schedule{

    protected $items=array();

    protected $leagueId;

    protected $teamId;

    protected $leagueName;

    protected $teamName;

    protected $teamUrl;

    public function __construct($leagueName, $teamName, $leagueId, $teamId, $teamUrl){
        $this->leagueName=$leagueName;
        $this->teamName=$teamName;
        $this->leagueId=$leagueId;
        $this->teamId=$teamId;
        $this->teamUrl=$teamUrl;
    }

    public function addScheduleItem(\DateTime $dateTime, $homeTeam, $awayTeam, $shortDate){
        $scheduleItem=new ScheduleItem($dateTime, $homeTeam, $awayTeam, $shortDate);

        $this->items[]=$scheduleItem;
    }

    public function toiCal($reminder){
        $iCalStr="BEGIN:VCALENDAR".PHP_EOL."VERSION:2.0".PHP_EOL."METHOD:PUBLISH".PHP_EOL.PHP_EOL;

        $iCalStr.="X-WR-CALNAME:".$this->teamName . " " .$this->leagueName . " MWSH".PHP_EOL;

        $iCalStr.="PRODID:".$this->teamName . " " . $this->teamId . " " .$this->leagueName . " " . $this->leagueId . PHP_EOL;

        /** @var ScheduleItem $item */
        foreach($this->items as $item){
            $iCalStr.="BEGIN:VEVENT".PHP_EOL;

            $iCalStr.="DTSTART:".$this->formatiCalDateTime($item->getStartTime()).PHP_EOL;
            $iCalStr.="DTEND:".$this->formatiCalDateTime($item->getEndTime()).PHP_EOL;
            $iCalStr.="SUMMARY:".$item->getDescription().PHP_EOL;
            $iCalStr.="SEQUENCE:0".PHP_EOL;
            $iCalStr.="UID:".$this->leagueId."-".$item->getShortTime()."-".$this->teamId.PHP_EOL;
            $iCalStr.="URL:".$this->teamUrl.PHP_EOL;
            $iCalStr.="DESCRIPTION:".$item->getDescription().PHP_EOL;

            if(!is_null($reminder)){
                $iCalStr.="BEGIN:VALARM".PHP_EOL;
                $iCalStr.="TRIGGER:-PT".(int)$reminder."M".PHP_EOL;
                $iCalStr.="X-WR-ALARMUID:"."ALARM-".$this->leagueId."-".$item->getShortTime()."-".$this->teamId.PHP_EOL;
                $iCalStr.="ACTION:DISPLAY".PHP_EOL;
                $iCalStr.="DESCRIPTION:".$item->getDescription().PHP_EOL;
                $iCalStr.="END:VALARM".PHP_EOL;
            }

            $iCalStr.="END:VEVENT".PHP_EOL;
        }

        $iCalStr.="END:VCALENDAR".PHP_EOL;

        return $iCalStr;
    }

    protected function formatiCalDateTime(\DateTime $dateTime){
        return $dateTime->format('Ymd\THis\Z');
    }
}