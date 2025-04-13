<?php
    require_once 'config.php';
    require_once 'router/route.php';

    require_once 'app/Controllers/api.champions.php';
    require_once 'app/Controllers/api.controller.php';
    require_once 'app/Controllers/api.skins.php';
    require_once 'app/Controllers/user.api.controller.php';

    $router = new Router();

    #                 endpoint      verb     controller      method
    $router->addRoute('champion',     'GET',    'ApiChampions', 'get'); # ApiChampions->get($params) returns all the champions
    $router->addRoute('champion',     'POST',   'ApiChampions', 'createChampion');
    $router->addRoute('champion/:Champion_id', 'GET',    'ApiChampions', 'getByID'); #Returns the champion with the submitted id
    $router->addRoute('champion/:Champion_id', 'PUT',    'ApiChampions', 'update');
    $router->addRoute('champion/:Champion_id', 'DELETE', 'ApiChampions', 'delete');


    $router->addRoute('Skins',     'GET',    'ApiSkins', 'get'); # ApiSkins->get($params) trae todas las skins
    $router->addRoute('Skins',     'POST',   'ApiSkins', 'createSkins');#trae la skin que coincida con el id numerico ingresado
    $router->addRoute('Skins/:Skin_id', 'GET',    'ApiSkins', 'getByID');
    $router->addRoute('Skins/:Skin_id', 'PUT',    'ApiSkins', 'update');
    $router->addRoute('Skins/:Skin_id', 'DELETE', 'ApiSkins', 'delete');
    
    
    $router->addRoute('user/token', 'GET',    'UserApiController', 'getToken'   ); # UserApiController->getToken()
    
    #               del htaccess resource=(), verbo con el que llamo GET/POST/PUT/etc
    $router->route($_GET['resource']        , $_SERVER['REQUEST_METHOD']);