<?php
// Swarm Config
const WASP_NUMBERS = array(
    'QUEEN' => array('quantity' => 1, 'life' => 80, 'damage' => 7),
    'WORKER' => array('quantity' => 5, 'life' => 68, 'damage' => 10),
    'DRONE' => array('quantity' => 8, 'life' => 60, 'damage' => 12)
);
/** Game Engine - Sets up game */
class GameEngine {
    private $wasp_nest = array();
    /** Constructor to build the wasp swarm objects */
    public function __construct(){
        foreach(WASP_NUMBERS as $type => $stats){
            $creation_counter = 0;
            do {
                $this->wasp_nest[] = new Wasp($type, $stats['life'], $stats['damage']);
                $creation_counter++;
            } while($creation_counter < $stats['quantity']);
        }
    }
    /** Render the game state to the screen */
    public function render_game_state() {
        echo '<ul>';
        foreach ($this->wasp_nest as $wasp){
            if($wasp->isAlive()) echo '<li>' .  $wasp->get_type() . ' --- ' . $wasp->get_life().'</li>';
        }
        echo '</ul>';
        if (isset($_SESSION['queen_dead'])){
            echo '  <h2>You have killed the swarm</h2>
                    <form action="index.php" method="post">
                        <input type="submit" value="Start Next Swarm" name="new_game">
                    </form>';
            session_destroy();
        }
        else{ ?>
        <form class="" action="index.php" method="post">
                        <input type="submit" value="Hit Random Wasp" name="swat">
                    </form>
            <?php
        }
    }
    /** Get status of remaining wasps in swarm and return the ones still alive */
    public function remaining_swarm(): array {
        $swarm = array_filter($this->wasp_nest, function ($wasp) {
            return $wasp->isAlive();
        });
        return $swarm;
    }
    /** Select wasp at random to be swatted, if its the queen check health after hit and flag if dead */
    public function swatRandomWasp()
    {
        $ramainingSwarm = $this->remaining_swarm();
        $swarmIndex = array_rand($ramainingSwarm);
        $ramainingSwarm[$swarmIndex]->swat();
        if($ramainingSwarm[$swarmIndex]->get_type() == "QUEEN"){
            if ($ramainingSwarm[$swarmIndex]->get_life() <= 0)
            {
                $_SESSION["queen_dead"] = true;
            }
        }
    }
}
/** Wasp class for creating wasp objects */
class Wasp {
    private $type, $life, $damage, $alive = true;
    /** Constructor to build wasp object including its type and stats */
    public function __construct(string $type, int $life, int $damage){
        $this->type = $type;
        $this->life = $life;
        $this->damage = $damage;
    }
    /** simulate damage taken from hit and flag if dead */
    public function swat()
    {
        $this->life -= $this->damage;
        if ($this->life <= 0) {
            $this->alive = false;
        }
    }
    /** return life status as boolean */
    public function isAlive(): bool {
        return $this->alive;
    }
    /** return wasps type */
    public function get_type(): string{
            return $this->type;
    }
    /** return wasps life remaining as integer */
    public function get_life(): int {
        return $this->life;
    }

}
//Start session and build html
session_start();
if (!isset($_SESSION["game_engine"]) || isset($_POST["new_game"])) $_SESSION["game_engine"] = new GameEngine();
if (isset($_POST["swat"])) $_SESSION["game_engine"]->swatRandomWasp();

?>
<!DOCTYPE html>
    <html>
        <head><title>NewIcon - Wasp Text Game</title></head>
        <body>
            <?php $_SESSION["game_engine"]->render_game_state(); ?>
        </body>
    </html>
