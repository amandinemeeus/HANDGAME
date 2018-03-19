<?php

include 'DAO.php';

$game = DAO::getGameBySession($_COOKIE["session"]);

DAO::handleNewTry($game, $_POST["letter"]);

header ('Location: index.php');