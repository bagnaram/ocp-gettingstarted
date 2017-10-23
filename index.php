<?php
require 'vendor/autoload.php';
#use \Slim\App;

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true
]];
$app = new \Slim\App($config);

$container = $app->getContainer();
$container['view'] = new \Slim\Views\Twig("templates/");

$app->get('/home', function ($request, $response, $args) {
  return $this->view->render($response, "home.html");
})->setName('homepage');


$app->get('/ldap/{query}', function ($request, $response, $args) {

  // $ds is a valid link identifier for a directory server

  // $person is all or part of a person's name, eg "Jo"

  $ldapserver = "ldap01.intranet.prod.int.rdu2.redhat.com";
  $ldapconnection = ldap_connect($ldapserver);

  $basedn = "dc=redhat,dc=com";

  $query = $args['query'];
  $filter="(|(sn=$query*)(givenname=$query*))";
  $justthese = array("ou", "sn", "givenname", "mail");

  $sr=ldap_search($ldapconnection, $basedn, $filter, $justthese);

  $info = ldap_get_entries($ldapconnection, $sr);

#  echo $info["count"]." entries returned\n";


  return $this->view->render($response, 'ldapsearch.html', [
      'query' => $query,
      'resultset' => $info
  ]);
})->setName('ldap');


// Run app
$app->run();

?>
