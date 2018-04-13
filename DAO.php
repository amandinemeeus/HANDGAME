<?php

include "game.php";

class DAO {

public static function getConnection(){
    $server = "localhost";
    $dbname = "weekend";
    $username = "root";
    $password = "root";

    $connection = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
    return $connection;
}

    public static function newGame(){
    try {
    
        $connection = DAO::getConnection();
        $statement = $connection -> prepare ("INSERT INTO hangman (session, word, progress, mistakes) 
                                                VALUES (UUID(), 'friends', '*******', 0)"); 
        $statement -> execute ();

        $id = $connection -> lastInsertId();
       
        $statement = $connection -> prepare ("SELECT session FROM hangman WHERE id = :id");
        $statement -> bindParam("id", $id);
        $statement -> execute ();
        $result = $statement -> fetch (PDO::FETCH_ASSOC);
        setcookie ("session", $result["session"]);
    }

    catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

}

public static function getGameBySession ($session){
    try {
    
        $connection = DAO::getConnection();
        $statement = $connection -> prepare ("SELECT * FROM hangman WHERE session = :session"); 
        $statement -> bindParam("session", $session);
        $statement -> execute();
        $result = $statement -> fetch (PDO::FETCH_ASSOC);
        $game = new Game;

        
        $game -> id = intval($result["id"]);
        $game -> session = $result["session"];
        $game -> word = $result["word"];
        $game -> progress = $result["progress"];
        $game -> mistakes = intval($result["mistakes"]); //transforme un string en integer

        return $game;

    }

    catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

public static function handleNewTry(Game $game, $letter){
    if (strpos($game -> word, $letter) === false ){
       DAO::incrementMistakes($game);
    } else {
        DAO::checkLetter($game, $letter[0]); //[0] Prendre uniquement le premier caractère
    }
   
}

public static function incrementMistakes(Game $game){
    try {
    
        $connection = DAO::getConnection();
        $statement = $connection -> prepare ("UPDATE hangman SET mistakes = mistakes + 1 WHERE session = :session"); 
        $statement -> bindParam("session", $game -> session);
        $statement -> execute(); 

    }

    catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

public static function checkLetter(Game $game, $letter){
    try {
        $session = $game -> session;
        $progress = $game -> progress;
        $connection = DAO::getConnection();
        for ($i = 0; $i < strlen ($game -> word); $i++){
            if ($game -> word[$i] == $letter){
                $progress[$i]=$letter;
            }
        };
        $statement = $connection -> prepare ("UPDATE hangman SET progress = :progress WHERE session = :session"); 
        $statement -> bindParam("session", $session);
        $statement -> bindParam("progress", $progress);
        $statement -> execute(); 

    }

    catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

}

//UUID = fonction pour générer un id pseudo-unique
//document.cookie JAVASCRIPT