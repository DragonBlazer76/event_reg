<?php

//get default theme based on global page config
function plgGetTheme(){
    global $GLOBAL_PAGECONFIG ;
    return $GLOBAL_PAGECONFIG->theme;
}

function plgTags( $id='', $autoInit=true, $theme='' ){
    $theme = $theme=="" ? plgGetTheme() : $theme;
    $tagId = $id!="" ? $id : "sgTag".md5(rand()) ;
    include VIEWS . "_themes/$theme/plugins/tags.php"; 
}

function plgQuickChat( $id='', $koLoop=false, $autoInit=true, $theme='' , $canAttach='can'){
    $theme = $theme=="" ? plgGetTheme() : $theme;
    $chatId = $id!="" ? $id : "sgQcm".md5(rand()) ;
    include VIEWS . "_themes/$theme/plugins/quickchat.php"; 
}

function plgDisplayTags( $tags=[], $label='Tags : ', $id='', $theme='' ){
    $theme = $theme=="" ? plgGetTheme() : $theme;
    if(count($tags)==0)
        return "";
    include VIEWS . "_themes/$theme/plugins/displayTags.php"; 
}

function plgConfirmBox( $id='', $theme='' ){
    $theme = $theme=="" ? plgGetTheme() : $theme;
    $id = $id!="" ? $id : "sgModalCB".md5(rand()) ;
    include VIEWS . "_themes/$theme/plugins/confirmationBox.php"; 
}

function plgPageTitle( $title='', $theme='' ){
    $theme = $theme=="" ? plgGetTheme() : $theme;
    if( $title==''){ 
        global $APPVARS;
        $title = @$APPVARS->page["pageTitle"] ;
    }
    include VIEWS . "_themes/$theme/plugins/pageTitle.php"; 
}

function plgFlashMessage( $message, $id='', $theme='' ){
    $theme = $theme=="" ? plgGetTheme() : $theme;
    $id = $id!="" ? $id : "sgFlashMsg".md5(rand()) ;
    $msgType = "info" ;
    if( is_array($message) ){
        $allFlashMessages = $message;
        foreach( $allFlashMessages as $mType=>$messages ){
            $msgType = $mType!="" ? $mType : $msgType ;
            $msgDesc = implode("<br />",array_column($messages, "message"));
            include VIEWS . "_themes/$theme/plugins/flashMessage.php"; 
        }
        return;
    }else{
        $msgDesc = $message ;
    }
    if( strlen($msgDesc)==0 ){
        return "";
    }
    
    include VIEWS . "_themes/$theme/plugins/flashMessage.php"; 
}

function plgRecipients( $id='', $emails=[], $importer=[], $theme='' ){
    $theme = $theme=="" ? plgGetTheme() : $theme;
    $id = $id!="" ? $id : "sgRecipients".md5(rand()) ;
    if( count($importer)==0 ){
        $importer = array("gmail","yahoo");
    }
    include VIEWS . "_themes/$theme/plugins/recipients.php"; 
}

?>