<?php
require 'vendor/autoload.php';
#use \Slim\App;

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails'    => true,
]];

// Data

$data             = array();
$data['mainmenu'] = array(
  array(
    'title'    => 'Home',
    'url'      => '/'
  ),
  array(
    'title'    => 'LDAP Demo',
    'url'      => '/ldap'
  ),
  array(
    'title'    => 'About',
    'url'      => '/about'
  ),
  array(
    'title'    => 'Properties',
    'url'      => '/properties'
  ),
  array(
    'title'    => 'Contact',
    'url'      => '/contact'
  ),
);

$app = new \Slim\App($config);

$container = $app->getContainer();
$container['view'] = new \Slim\Views\Twig("templates/");



$app->add(function ($request, $response, $next) use ($data) {
    $data['current_url'] = $request->getURI()->getPath();
    return $next($request, $response);
});

$app->get('/', function ($request, $response, $args) use ($data) {
  $data['current_url'] = '/';
  return $this->view->render($response, 'home.html', $data);
})->setName('homepage');

$app->get('/contact', function ($request, $response, $args) use ($data) {
  $data['current_url'] = '/contact';
  return $this->view->render($response, 'contact.html', $data);
})->setName('contact');

$app->get('/about', function ($request, $response, $args) use ($data) {
  $data['current_url'] = '/about';
  return $this->view->render($response, 'about.html', $data);
})->setName('about');

$app->get('/properties', function ($request, $response, $args) use ($data) {
  $data['current_url'] = '/properties';
  $data['vars'] = $_SERVER;
  $data['env'] = $_ENV;
  return $this->view->render($response, 'properties.html', $data);
})->setName('properties');

$app->get('/ldap[/{query}]', function ($request, $response, $args) use ($data) {
  $data['current_url'] = '/ldap';

  // $ds is a valid link identifier for a directory server

  // $person is all or part of a person's name, eg "Jo"

  $ldapserver = "ldap01.intranet.prod.int.rdu2.redhat.com";
  $ldapconnection = ldap_connect($ldapserver);

  $basedn = "dc=redhat,dc=com";

  if($args['query'] && $args['query'] != "")
  {
    $query = $args['query'];
  }
  else
  {
    $query = "Enter query here";
  }
  $filter="(|(sn=$query*)(givenname=$query*))";
  $justthese = array("ou", "sn", "givenname", "mail");

  $sr=ldap_search($ldapconnection, $basedn, $filter, $justthese);

  $info = ldap_get_entries($ldapconnection, $sr);

#  echo $info["count"]." entries returned\n";

  $data['query']     = $query;
  $data['resultset'] = $info;
  return $this->view->render($response, 'ldapsearch.html', $data);
} )->setName('ldap');


// Run app
$app->run();

?>
