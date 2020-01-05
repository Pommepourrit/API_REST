<?php

require_once '../vendor/autoload.php';
 
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

//TODO Ou mettre la configuration de l'API : header type de retour etc etc 
//TODO dans get/castings ne renvoi pas toutes les infos du castings
//castings/{id} renvoie toutes les infos du casting voulu
//TODO requête avec authentification

try
{
    // Init basic route
    $route1 = new Route(
      '/api/castings/p={pageCourante}',
      [
          'controller' => My\Controller\CastingController::class,
          'method'     => 'getCastings',
      ],
      [
           'pageCourante' => '[0-9]+'
      ]
    );
 
    // Init route with dynamic placeholders
    $route2 = new Route(
        '/api/castings/{id}',
        [
            'controller' => My\Controller\CastingController::class,
            'method'     => 'getCasting',
        ],
        [
            'id' => '[0-9]+'
        ]
    );
    
    $route3 = new Route(
        '/api/castings/domaine={domaine}',
        [
            'controller' => My\Controller\CastingController::class,
            'method'     => 'getCastingsByDomaine',
        ],
        [
            'domaine' => '[a-zA-Z]+'
        ]          
    );
    
    $route4 = new Route(
        '/api/castings/annonceur={anonceur}',
        [
            'controller' => My\Controller\CastingController::class,
            'method'     => 'getCastingsByAnnonceur',
        ],
        [
            'annonceur' => '[a-zA-Z]+'
        ]          
    );
    
    $route5 = new Route(
        '/api/castings/count',
        [
            'controller' => My\Controller\CastingController::class,
            'method'     => 'getCount',
        ]         
    );
    
 
    // Add Route object(s) to RouteCollection object
    $routes = new RouteCollection();
    $routes->add('api_castings', $route1);
    $routes->add('api_casting', $route2);
    $routes->add('api_castingsByDomaine', $route3);
    $routes->add('api_castingsByAnnonceur', $route4);
    $routes->add('api_countCasting', $route5);
    
    
 
    // Init RequestContext object
    $context = new RequestContext();
    
    $request = Request::createFromGlobals();

    $context->fromRequest($request);

    // Init UrlMatcher object
    $matcher = new UrlMatcher($routes, $context);

    // Find the current route
    $parameters = $matcher->match($context->getPathInfo());
    
    $controllerName = $parameters['controller'];
    $method         = $parameters['method'];
    
    unset($parameters['controller']);
    unset($parameters['method']);
    unset($parameters['_route']);
    
    $controller = new $controllerName();
    
    call_user_func_array([$controller, $method], $parameters);
}
catch (ResourceNotFoundException $e)
{
  echo $e->getMessage();
}