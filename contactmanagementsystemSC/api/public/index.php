<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

//add database
use App\Models\DB;
// GITHUB
// new

require_once __DIR__ . '/../vendor/autoload.php';

// functions definition start
function getDatabase() {

    $dbhost="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="contact";

    $db = new DB($dbhost, $dbuser, $dbpass, $dbname);
    return $db;
 }
// functions definition end

$app = AppFactory::create();

//Slim's implementation of PSR-7 does not support the sending of data in a JSON format, 
//instead, they provide a BodyParsingMiddleware that handles this task
$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

//http://localhost/slim4-sample/api
$app->get('/', function (Request $request, Response $response) {    
    $response->getBody()->write('Slim4 Contact API - all ok - up and running!');
    return $response;
});

//---------------- CRUD contact start ---------------------------
//
//(C)reate - SQL insert contact owned by ownerlogin
//http://localhost/slim4-sample/api/getapi/contacts
$app->post('/getapi/contacts', function (Request $request, Response $response) {
    
    $data = $request->getParsedBody();
    $name = $data["name"];
    $email = $data["email"];
    $mobileno = $data["mobileno"];

    $db = getDatabase();

    //dbs => db operation status
    $dbs = $db->insertContact($name, $email, $mobileno);
    $db->close();

    $data = array(
        "insertstatus" => $dbs->status,
        "error" => $dbs->error,
        "id" => $dbs->lastinsertid,
        "name" => $name,
        "email" => $email,
        "mobileno" => $mobileno
    ); 

    $response->getBody()->write(json_encode($data));
  
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

//(R)ead - SQL select all
//http://localhost/slim4-sample/api/getapi/contacts
$app->get('/getall/contacts', function (Request $request, Response $response) {

    $db = getDatabase();
    $data = $db->getAllContacts();
    $db->close();

    $response->getBody()->write(json_encode($data));
        
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);    
});

//(R)ead by {id} - SQL select contact by id
//http://localhost/slim4-sample/api/getapi/contacts/{id}
$app->get('/getapi/contacts/{id}', function (Request $request, Response $response, array $args) {

    $id = $args["id"];

    $db = getDatabase();
    $data = $db->getContactViaId($id);
    $db->close();

    $response->getBody()->write(json_encode($data));
        
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

//(U)pdate - SQL update contact by id
//http://localhost/slim4-sample/api/getapi/contacts/{id}
$app->put('/getapi/contacts/{id}', function (Request $request, Response $response, array $args) {

    $id = $args["id"];

    $data = $request->getParsedBody();
    $name = $data["name"];
    $email = $data["email"];
    $mobileno = $data["mobileno"];

    $db = getDatabase();
    //dbs => db operation status
    $dbs = $db->updateContactViaId($id, $name, $email, $mobileno);
    $db->close();

    $data = array(
        "updatestatus" => $dbs->status,
        "error" => $dbs->error,
        "id" => $id,
        "name" => $name,
        "email" => $email,
        "mobileno" => $mobileno
    );
    
    $response->getBody()->write(json_encode($data));
  
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

//(D)elete - SQL delete by id
//http://localhost/slim4-sample/api/delete/contacts/{id}
$app->get('/deleteapi/contacts/{id}', function (Request $request, Response $response, array $args) {

    $id = $args["id"];

    $db = getDatabase();
    //dbs => db operation status
    $dbs = $db->deleteContactViaId($id);
    $db->close();

    if ($dbs->status === true) {
        $data = array(
            "deletestatus" => true,
            "message" => "User deleted successfully."
        );
        $statusCode = 200;
    } else {
        $data = array(
            "deletestatus" => false,
            "message" => "Failed to delete user."
        );
        $statusCode = 500;
    }

    $response->getBody()->write(json_encode($data));

    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus($statusCode);

});
//
//---------------- CRUD contact end -----------------------------

$app->run();