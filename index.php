<?php

include 'DAO.php';

if (isset($_COOKIE["session"])){
    $game = DAO::getGameBySession($_COOKIE["session"]);
    
echo "Mistakes : " . $game -> mistakes;
echo "<br>";
echo "Progress : " . $game -> progress;

?>  <form action="new_try.php" method="post">
        <input type="text" name="letter" placeholder="Insert a letter">
        <button type="submit">Send</button>
    </form> 

<?php

} else {
    ?>
    <form action="new_game.php" >
        <button type="submit">New game</button>
    </form>
     <?php
}

//F12 sur fonction 