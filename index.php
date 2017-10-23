<?php
require 'vendor/autoload.php';
use \Slim\App;

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
]];
$app = new App($config);

$app->get('/', function ($request, $response, $args) {
  return $this->view->render($response, 'home.html', [
      'current' => 'home'
  ]);
})->setName('current');


$app->get('/ldap/{query}', function ($request, $response, $args) {

  // $ds is a valid link identifier for a directory server

  // $person is all or part of a person's name, eg "Jo"

  $ds = "ldap://ldap01.intranet.prod.int.rdu2.redhat.com/";
  $dn = "dc=redhat,dc=com";

  $query = $args['query']
  $filter="(|(sn=$query*)(givenname=$query*))";
  $justthese = array("ou", "sn", "givenname", "mail");

  $sr=ldap_search($ds, $dn, $filter, $justthese);

  $info = ldap_get_entries($ds, $sr);

#  echo $info["count"]." entries returned\n";


  return $this->view->render($response, 'ldapsearch.html', [
      'query' => $query,
      'resultset' => $info
  ]);
})->setName('ldap');


// Run app
$app->run();

?>
