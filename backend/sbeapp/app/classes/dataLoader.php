<?php

class sgDataLoader {

    function __construct() {
//        global $APPVARS, $GLOBAL_CONFIG, $app ;
//        $this->app = $app ;
//        $this->config = $GLOBAL_CONFIG ;
//        $this->lang = $this->config->lang ;
//        $this->db = new mongoDb();
    }

    //check whether the data has been loaded
    function check($method) {
        global $APPDATA;
        return isset($APPDATA->$method) ? $APPDATA->$method : false;
    }

    function load($method, $inlineJs = "", $preloadedData = []) {
        global $APPDATA;

        //check if loaded
        $data = $this->check($method);

        //not loaded, allow to load the data by calling the function name in var $method
        if (!$data && method_exists('sgDataLoader', $method)) {
            $data = $this->$method();
            $APPDATA->$method = $data;
        }

        if (count($preloadedData) > 0) {
            $data = $preloadedData;
            $APPDATA->$method = $data;
        }

        if ($inlineJs == "inline-js" || $inlineJs == true) {
            $this->inlineJS($method, $data);
        }

        return $data;
    }

    //load the language
    function loadCountries() {
        $results = $this->db->find("countries", [], [], 0, array(
            "name" => 1,
            "ccode1" => 1,
            "ccode2" => 1,
            "currencycode" => 1
        ));
        foreach ($results as $key => $subs) {
            foreach ($subs as $key1 => $value2) {
                if ($key1 == "_id") {
                    unset($results[$key][$key1]);
                }
            }
        }
        return $results;
    }

    function loadBannedWords() {
        return true;
    }

    function loadDirectoryTags() {
        return true;
        $keys = array("category" => 1);
        $sArrTags = array();
        $initial = array("skills" => array());
        $reduce = "function (obj, prev) { prev.skills.push(obj.skill); }";
        $tags = $this->db->groupBy('directory', $keys, $initial, $reduce);

        for ($X = 0; $X < count($tags); $X++) {
            $directoryItem = $tags[$X];
            if (count(@$directoryItem['skills']) > 0) {
                for ($sk = 0; $sk < count($directoryItem['skills']); $sk++) {
                    array_push($sArrTags, $directoryItem['skills'][$sk]);
                }
            }
        }//FOR

        return $sArrTags;
    }

    function inlineJS($method, $data) {
        global $APPVARS;
        $inliner = isset($APPVARS->inlineJs) ? $APPVARS->inlineJs : array();
        $inliner[$method] = $data;
        $APPVARS->inlineJs = $inliner;
    }

    function addModule($name, $params) {
        global $APPVARS;
        $modParams = isset($APPVARS->module) ? $APPVARS->module : array();
        $modParams[$name] = $params;
        $APPVARS->module = $modParams;
//        printArray($APPVARS->module);
    }

    function loadConfig($name) {
        if ($name == "") {
            return;
        }
        global $APPVARS;
        $cfg = array();
        
        if (isset($APPVARS->envConfig->$name)) {
            $cfg = $APPVARS->envConfig->$name;
            if (isset($cfg['appSecret']))
                unset($cfg['appSecret']);
            if (isset($cfg['domain']))
                unset($cfg['domain']);
            
        }else if( $name=="proposalConfig" || $name=="proposalconfig" ){
            global $PAGECONFIG, $GLOBAL_CONFIG;
            $cfg = array_merge($PAGECONFIG->proposals, $PAGECONFIG->adminCharges);
            if( isset($GLOBAL_CONFIG->payment['currency']) )
                $cfg['currency'] = $GLOBAL_CONFIG->payment['currency'];
            if( isset($GLOBAL_CONFIG->payment['currencyCode']) )
                $cfg['currencyCode'] = $GLOBAL_CONFIG->payment['currencyCode'];
            unset($GLOBAL_CONFIG->payment['salesCost']);
            unset($GLOBAL_CONFIG->payment['reddotSalesCost']);
        } //end if config
        
        if( count($cfg)>0 )
            $this->load('cfg' . ucwords($name), true, $cfg);
    }

}

?>