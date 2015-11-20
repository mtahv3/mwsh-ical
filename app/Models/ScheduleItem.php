<?php
namespace App\Models;

use DateTime;

class ScheduleItem{

    /**
     * @var DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @var string $homeTeam
     */
    protected $homeTeam;

    /**
     * @var string $awayTeam
     */
    protected $awayTeam;

    /**
     * @var string $shortTime
     */
    protected $shortTime;

    public function __construct(DateTime $dateTime, $homeTeam, $awayTeam, $shortTime){
        //For iCal Support, we want the times to be in UTC time so timezone support works
        //correctly
        $this->dateTime = $dateTime->setTimezone(new \DateTimeZone("UTC"));
        $this->homeTeam=$homeTeam;
        $this->awayTeam=$awayTeam;
        $this->shortTime=$shortTime;
    }

    public function getStartTime(){
        return $this->dateTime;
    }

    public function getEndTime(){
        //since DateTime::add() will actually modify
        //the object and not return a new one, we
        //need to clone the object and modify a copy
        //so we can keep the original DateTime obj
        $date=clone $this->dateTime;
        $date->add(\DateInterval::createFromDateString("1 hour"));

        return $date;
    }

    public function getDescription(){
        return $this->homeTeam . " vs " . $this->awayTeam;
    }

    public function getShortTime(){
        return $this->shortTime;
    }

}

