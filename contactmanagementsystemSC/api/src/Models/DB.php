<?php

namespace App\Models;

use \PDO;
use \DateTime;

class Contact {
    var $id;
    var $name;
    var $email;
    var $mobileno;
    var $photo;
    var $status;
    var $addeddate;    
}

class DbStatus {
    var $status;
    var $error;
    var $lastinsertid;
}

function time_elapsed_string($datetime, $full = false) {

    $now = new DateTime;

    $ago = new DateTime($datetime);

    $diff = $now->diff($ago);
    //$diff->h = $diff->h - 5;

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];

    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

class DB
{
    protected $dbhost;
    protected $dbuser;
    protected $dbpass;
    protected $dbname;    
    protected $db;

 	function __construct( $dbhost, $dbuser, $dbpass, $dbname) {
   		$this->dbhost = $dbhost;
   		$this->dbuser = $dbuser;
   		$this->dbpass = $dbpass;
   		$this->dbname = $dbname;

   		$db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
    	$this->db = $db;
   	}

    function close() {

        try {
           $this->db = null;   
        }
        catch(PDOException $e) {
           $errorMessage = $e->getMessage();
           return 0;
        } 
    }    

    //(C)reate - sql insert contact
    function insertContact($name, $email, $mobileno) {

        try {
           
            $sql = "INSERT INTO contacts(name, email, mobile_no, added_date) 
                    VALUES (:name, :email, :mobileno, NOW())";

            $stmt = $this->db->prepare($sql);  
            $stmt->bindParam("name", $name);
            $stmt->bindParam("email", $email);
            $stmt->bindParam("mobileno", $mobileno);
            $stmt->execute();

            $dbs = new DbStatus();
            $dbs->status = true;
            $dbs->error = "none";
            $dbs->lastinsertid = $this->db->lastInsertId();

            return $dbs;
        }
        catch(PDOException $e) {
            $errorMessage = $e->getMessage();

            $dbs = new DbStatus();
            $dbs->status = false;
            $dbs->error = $errorMessage;

            return $dbs;
        }          
    }  
    
    //(R)ead - sql select all contact - get all contacts
    function getAllContacts() {

        $sql = "SELECT *
                FROM contacts";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(); 
        $row_count = $stmt->rowCount();

        $data = array();

        if ($row_count)
        {
           while($row = $stmt->fetch(PDO::FETCH_ASSOC))
           {
                $contact = new Contact();
                $contact->id = $row['id'];
                $contact->name = $row['name'];
                $contact->email = $row['email'];
                $contact->mobileno = $row['mobile_no'];
                $contact->photo = $row['photo'];
                $contact->status = $row['status']; 

                $addeddate = $row['added_date'];
                $contact->addeddate = time_elapsed_string($addeddate);                

                array_push($data, $contact);
           }
        }

        return $data;
    }    

    //(R)ead - sql select contact via id and ownerlogin
    function getContactViaId($id) {

        $sql = "SELECT *
                FROM contacts
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute(); 
        $row_count = $stmt->rowCount();

        $contact = new Contact();

        if ($row_count)
        {
            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {               
                $contact = new Contact();
                $contact->id = $row['id'];
                $contact->name = $row['name'];
                $contact->email = $row['email'];
                $contact->mobileno = $row['mobile_no'];
                $contact->photo = $row['photo'];
                $contact->status = $row['status']; 

                $addeddate = $row['added_date'];
                $contact->addeddate = time_elapsed_string($addeddate); 
            }
        }

        return $contact;
    }

    //(U)pdate - sql update contact via id - update contact via id
    function updateContactViaId($id, $name, $email, $mobileno) {

        $sql = "UPDATE contacts
                SET name = :name,
                    email = :email,
                    mobile_no = :mobileno
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);  
            $stmt->bindParam("id", $id);
            $stmt->bindParam("name", $name);    
            $stmt->bindParam("email", $email);
            $stmt->bindParam("mobileno", $mobileno);
            $stmt->execute();

            $dbs = new DbStatus();
            $dbs->status = true;
            $dbs->error = "none";

            return $dbs;
        }
        catch(PDOException $e) {
            $errorMessage = $e->getMessage();

            $dbs = new DbStatus();
            $dbs->status = false;
            $dbs->error = $errorMessage;

            return $dbs;
        } 
    }    

    //delete contact via id
    function deleteContactViaId($id) {

        $dbstatus = new DbStatus();

        $sql = "DELETE 
                FROM contacts 
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql); 
            $stmt->bindParam("id", $id);
            $stmt->execute();

            $dbstatus->status = true;
            $dbstatus->error = "none";
            return $dbstatus;
        }
        catch(PDOException $e) {
            $errorMessage = $e->getMessage();

            $dbstatus->status = false;
            $dbstatus->error = $errorMessage;
            return $dbstatus;
        }           
    }
}