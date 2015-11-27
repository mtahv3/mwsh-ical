<?php

namespace App\Http\Controllers\QHL;

use App\Exceptions\QHL\URLNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\DomParser;
use Illuminate\Support\Facades\Cache;

class LeagueController extends Controller{

    public function getLeagues(){
        $leagueUrl=$this->getLeaguesUrl();

        $parser=new DomParser();
        $dom=$parser->getDOMDocumentFromURL($leagueUrl);

        $daysAndTeams=$this->parseTeamsByDay($dom, $parser);

        return json_encode($daysAndTeams, JSON_PRETTY_PRINT);
    }

    protected function parseTeamsByDay(\DOMDocument $dom, DomParser $parser){
        $headerNodes=$parser->getElementsByClassName($dom, "panel-heading");
        $teamsNodes=$parser->getElementsByClassName($dom, "panel-body");

        $teamsByDay=array();

        for($i=0;$i<$headerNodes->length;$i++){
            $array=array();
            $headerNode=$headerNodes[$i];
            $array['day']=$headerNode->nodeValue;

            $teamNode=$teamsNodes[$i];

            foreach($teamNode->childNodes as $child){

                if($child->tagName=="p") {
                    $leagueUrl=$child->firstChild->getAttribute("href");
                    $leagueUrlPieces=explode("=", $leagueUrl);
                    if(count($leagueUrlPieces) == 2){
                        $leagueId=$leagueUrlPieces[1];
                    }else{
                        $leagueId=0;
                    }
                    $array['teams'][] = array("name"=>$child->nodeValue, "id"=>$leagueId);
                }
            }

            if(isset($array["teams"]) && count($array["teams"])){
                $teamsByDay[]=$array;
            }
        }

        return $teamsByDay;
    }

    protected function getLeaguesUrl(){
        $leagueUrl=getenv("QHL_ALL_LEAGUES_URL");

        if($leagueUrl===false){
            throw new URLNotFoundException;
        }

        return $leagueUrl;
    }


}