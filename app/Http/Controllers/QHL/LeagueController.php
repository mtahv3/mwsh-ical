<?php

namespace App\Http\Controllers\QHL;

use App\Exceptions\QHL\URLNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\DomParser;
use Illuminate\Support\Facades\Cache;

class LeagueController extends Controller{

    public function getLeagues(){

        if(!$json=$this->tryToGetFromCache()){
            $leagueUrl=$this->getLeaguesUrl();

            $parser=new DomParser();
            $dom=$parser->getDOMDocumentFromURL($leagueUrl);

            $daysAndLeagues=$this->parseLeaguesByDay($dom, $parser);

            $json=json_encode($daysAndLeagues, JSON_PRETTY_PRINT);

            $this->addToCache($json);
        }

        return $json;
    }

    protected function tryToGetFromCache(){
        $retVal=false;

        if(Cache::has("LEAGUES")){
            $retVal=Cache::get("LEAGUES");
        }

        return $retVal;
    }

    protected function addToCache($leagueJson){
        $expireMinutes = (getenv("REDIS_EXPIRE_MINUTES") ? getenv("REDIS_EXPIRE_MINUTES") : 60);

        Cache::put("LEAGUES", $leagueJson, $expireMinutes);
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

    protected function getLeaguesUrl(){
        $leagueUrl=getenv("QHL_ALL_LEAGUES_URL");

        if($leagueUrl===false){
            throw new URLNotFoundException;
        }

        return $leagueUrl;
    }
}