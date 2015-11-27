<?php

namespace App\Models;

use \DOMDocument;

class DomParser{

    /**
     * @param string $url
     * @return DOMDocument
     */
    public function getDOMDocumentFromURL($url){
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
}