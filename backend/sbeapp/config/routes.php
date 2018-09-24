<?php
global $ROUTES ;
$ROUTES = array(
    //static pages routes
    '/forbidden' => 'sp.errorPages.403',
    '/page-not-found' => 'sp.errorPages.404',
    '/server-error' => 'sp.errorPages.500',
    
    //home controller routes
    '/' => 'home.login',
    '/dashboard' => 'guests.index',
    '/login' => 'home.login',
    '/logout' => 'home.logout',
    
    '/forgot' => 'home.forgot',
    '/reset' => 'home.forgot',
    
    '/user' => 'user.index',
    '/profile' => 'user.index',
    
    '/events/new' => 'events.form',
    '/events/edit' => 'events.form',
    
    '/guests/new' => 'guests.form',
    '/guests/edit' => 'guests.form',
    '/guests/save' => 'guests.save'
        
);
?>
