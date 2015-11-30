<?php

namespace App\Http\Controllers\QHL;

use App\Exceptions\QHL\URLNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\DomParser;
use Illuminate\Support\Facades\Cache;

class LeagueController extends Controller{

    public function getLeagues(){

        if(!$json=$this->tryToGetLeaguesFromCache()){
            $leagueUrl=$this->getAllLeaguesUrl();

            $parser=new DomParser();
            $dom=$parser->getDOMDocumentFromURL($leagueUrl);

            $daysAndLeagues=$this->parseLeaguesByDay($dom, $parser);

            $json=json_encode($daysAndLeagues, JSON_PRETTY_PRINT);

            $this->addLeaguesToCache($json);
        }

        return $json;
    }

    public function getTeams($leagueId){
        if(!$json=$this->tryToGetTeamsFromCache($leagueId)) {
            $leagueUrl = $this->getLeagueUrl($leagueId);

            $parser = new DomParser();
            $dom = $parser->getDOMDocumentFromURL($leagueUrl);

            $teamsInLeague = $this->parseTeamsByLeague($dom);
            $json=json_encode($teamsInLeague, JSON_PRETTY_PRINT);

            $this->addTeamsToCache($json, $leagueId);
        }

        return $json;
    }

    protected function parseTeamsByLeague(\DOMDocument $dom){

        $teams=array();

        $tables=$dom->getElementsByTagName("table");

        if($tables->length > 0){
            $teamsTable=$tables[0];

            $tableBodies=$teamsTable->getElementsByTagName("tbody");

            if($tableBodies->length==1){
                $tableBody=$tableBodies[0];

                $tableRows=$tableBody->getElementsByTagName("tr");

                foreach($tableRows as $tableRow){
                    $tableCells=$tableRow->getElementsByTagName("td");

                    if($tableCells->length == 11){
                        $teamNameCell=$tableCells[1];
                        $teamName=$teamNameCell->nodeValue;

                        $teamLink = $teamNameCell->firstChild->getAttribute("href");
                        $linkPieces = explode("TeamID=", $teamLink);

                        if(count($linkPieces) == 2){
                            $teamId = $linkPieces[1];

                            $teams[]=array("name"=>$teamName, "id"=>$teamId);
                        }
                    }
                }
            }


        }

        return $teams;
    }

    protected function tryToGetLeaguesFromCache(){
        $retVal=false;

        if(Cache::has("LEAGUES")){
            $retVal=Cache::get("LEAGUES");
        }

        return $retVal;
    }

    protected function tryToGetTeamsFromCache($leagueId){
        if(Cache::has("TEAMS-".$leagueId)){
            return Cache::get("TEAMS-".$leagueId);
        }

        return false;
    }

    protected function addLeaguesToCache($leagueJson){
        $expireMinutes = (getenv("REDIS_EXPIRE_MINUTES") ? getenv("REDIS_EXPIRE_MINUTES") : 60);

        Cache::put("LEAGUES", $leagueJson, $expireMinutes);
    }

    protected function addTeamsToCache($teamsJson, $leagueId){
        $expireMinutes = (getenv("REDIS_EXPIRE_MINUTES") ? getenv("REDIS_EXPIRE_MINUTES") : 60);

        Cache::put("TEAMS-".$leagueId, $teamsJson, $expireMinutes);
    }

    protected function parseLeaguesByDay(\DOMDocument $dom, DomParser $parser){
        $headerNodes=$parser->getElementsByClassName($dom, "panel-heading");
        $leaguesNodes=$parser->getElementsByClassName($dom, "panel-body");

        $leaguesByDay=array();

        for($i=0;$i<$headerNodes->length;$i++){
            $array=array();
            $headerNode=$headerNodes[$i];
            $array['day']=$headerNode->nodeValue;

            $leagueNode=$leaguesNodes[$i];

            foreach($leagueNode->childNodes as $child){

                if($child->tagName=="p") {
                    $leagueUrl=$child->firstChild->getAttribute("href");
                    $leagueUrlPieces=explode("=", $leagueUrl);
                    if(count($leagueUrlPieces) == 2){
                        $leagueId=$leagueUrlPieces[1];
                    }else{
                        $leagueId=0;
                    }
                    $array['leagues'][] = array("name"=>$child->nodeValue, "id"=>$leagueId);
                }
            }

            if(isset($array["leagues"]) && count($array["leagues"])){
                $leaguesByDay[]=$array;
            }
        }

        return $leaguesByDay;
    }

    protected function getAllLeaguesUrl(){
        $allLeaguesUrl=getenv("QHL_ALL_LEAGUES_URL");

        if($allLeaguesUrl===false){
            throw new URLNotFoundException;
        }

        return $allLeaguesUrl;
    }

    protected function getLeagueUrl($leagueId){
        $leagueUrl=getenv("QHL_LEAGUE_URL");

        if($leagueUrl===false){
            throw new URLNotFoundException;
        }

        $leagueUrl=str_replace("{LID}", $leagueId, $leagueUrl);

        return $leagueUrl;
    }
}