<?php

namespace App\Http\Controllers\QHL;

use App\Http\Controllers\Controller;
use \DOMDocument;
use \DateTime;
use App\Models\Schedule;
use Illuminate\Support\Facades\Cache;

class ScheduleController extends Controller{

    public function getSchedule($leagueId, $teamId)
    {
        $cachedCalendar=$this->tryToGetFromCache($leagueId,$teamId);
        if($cachedCalendar){
            $this->outputiCal($cachedCalendar);
        }else{

            $scheduleArray=$this->fetchAndParseSchedule($leagueId, $teamId);

            //print_r($scheduleArray);
            $schedule=new Schedule($scheduleArray["leagueName"], $scheduleArray["teamName"], $leagueId, $teamId, $scheduleArray["teamUrl"]);

            foreach($scheduleArray["schedule"] as $item){
                $schedule->addScheduleItem($item["DateTime"], $item["HomeTeam"], $item["AwayTeam"], $item["ShortDate"]);
            }

            $iCalText=$schedule->toiCal();

            $this->addToCache($iCalText, $leagueId, $teamId);

            $this->outputiCal($iCalText);
        }
    }

    protected function outputiCal($iCalText){
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename=calendar.ics');

        echo $iCalText;
    }

    protected function fetchAndParseSchedule($leagueId, $teamId){
        $urls=$this->getLeagueAndTeamUrl($leagueId, $teamId);
        $leagueUrl=$urls["LeagueURL"];
        $teamUrl=$urls["TeamURL"];

        $dates=$this->getLeagueDates($leagueUrl);

        return $this->getTeamSchedule($teamUrl, $dates);
    }

    protected function getTeamSchedule($teamUrl, $dates){
        $dom=$this->getDOMDocumentFromURL($teamUrl);

        $schedule=array();

        $schedule["teamUrl"]=$teamUrl;

        $header=$dom->getElementsByTagName("h1");
        if($header->length==1){
            $schedule["leagueName"]=trim(preg_replace('/\s\s+/', ' ', $header->item(0)->nodeValue));
        }



        //Page should have 3 tables, currently the 3rd table is the one we want.
        //so let's do a little DOM parsing with PHP's excellent parsing capabilities.
        $tables = $dom->getElementsByTagName("table");
        if ($tables->length == 3) {
            $schedule["teamName"]= $tables->item(1)->nodeValue;

            //get 3rd table - zero indexed
            $scheduleTable = $tables->item(2);

            //get the first table body
            $scheduleTableBody = $scheduleTable->getElementsByTagName('tbody')->item(0);

            //get all table rows
            $tableRows = $scheduleTableBody->getElementsByTagName("tr");

            //counter to determine which week we are on, so we can get the full
            //date from the dates array
            $weekCounter=0;

            //Each table row should have 5 table cells in the following format
            // --------------------------------------------
            // | Date | Time | Home Team | vs | Away Team |
            // --------------------------------------------
            foreach ($tableRows as $tableRow) {
                $cells = $tableRow->getElementsByTagName("td");

                if ($cells->length == 5) {


                    if(isset($dates[$weekCounter])){
                        $shortDate=$cells->item(0)->nodeValue;
                        $time = $cells->item(1)->nodeValue;
                        $homeTeam = $cells->item(2)->nodeValue;
                        $awayTeam = $cells->item(4)->nodeValue;
                        $date=$dates[$weekCounter];

                        //date example: Thursday, May 14, 2015
                        //time example: 10:10 PM

                        $dateAndTime=$date . " " . $time;

                        $dateTime = DateTime::createFromFormat("l, F d, Y g:i A", $dateAndTime, new \DateTimeZone("America/Chicago"));

                        $schedule["schedule"][]=["ShortDate"=>$shortDate, "HomeTeam"=>$homeTeam, "AwayTeam"=>$awayTeam, "DateTime"=>$dateTime];
                    }

                    $weekCounter++;
                }
            }
        }

        return $schedule;
    }

    protected function getLeagueDates($leagueUrl){
        $dom=$this->getDOMDocumentFromURL($leagueUrl);

        $datesArray=array();

        $tables=$dom->getElementsByTagName("table");
        if($tables->length==3){
            /**
             * Table should be in the following format
             * ----------------------------------
             * | Thursday, March 05, 2015       |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             * | Thursday, March 12, 2015       |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             * | Thursday, March 12, 2015       |
             * ----------------------------------
             * | Playoffs                       |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             * | Date | Time | Home | vs | Away |
             * ----------------------------------
             *
             * The date cells have a colspan of 6
             * The "Playoffs" cell has a colspan of 5
             */
            $scheduleTable=$tables->item(2);

            $scheduleTableBody=$scheduleTable->getElementsByTagName("tbody")->item(0);

            $tableRows=$scheduleTableBody->getElementsByTagName("tr");

            foreach($tableRows as $tableRow){
                $cells=$tableRow->getElementsByTagName("td");

                if($cells->length == 1){
                    $cell=$cells->item(0);

                    $colSpan=$cell->getAttribute("colspan");
                    if($colSpan=="6") {
                        $date=$cells->item(0)->nodeValue;


                        $datesArray[]=$date;
                    }
                }
            }

        }

        return $datesArray;
    }

    protected function tryToGetFromCache($leagueId, $teamId){
        $retVal=false;

        if(Cache::has("SCHEDULE-".$leagueId."-".$teamId)){
            $retVal=Cache::get("SCHEDULE-".$leagueId."-".$teamId);
        }

        return $retVal;
    }

    protected function addToCache($icalString, $leagueId, $teamId){
        $expireMinutes = (getenv("REDIS_EXPIRE_MINUTES") ? getenv("REDIS_EXPIRE_MINUTES") : 60);

        Cache::put("SCHEDULE-".$leagueId."-".$teamId, $icalString, $expireMinutes);
    }

    protected function getDOMDocumentFromURL($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);

        $doc = new \DOMDocument();
        //php DOM document doesn't work well with HTML 5 and non-well formatted
        //HTML, so let's just suppress the warnings for now (not the best method, but
        //it works)
        @$doc->loadHTML($content);
        $doc->preserveWhiteSpace = false;

        return $doc;
    }

    protected function getLeagueAndTeamUrl($leagueId, $teamId){
        $teamUrl=getenv("QHL_TEAM_URL");
        $leagueUrl=getenv("QHL_LEAGUE_URL");

        $teamUrl=str_replace("{LID}", $leagueId, $teamUrl);
        $teamUrl=str_replace("{TID}", $teamId, $teamUrl);

        $leagueUrl=str_replace("{LID}", $leagueId, $leagueUrl);

        return array("TeamURL"=>$teamUrl, "LeagueURL"=>$leagueUrl);
    }
}