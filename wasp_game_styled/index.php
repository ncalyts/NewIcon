<?php
// Swarm Config
const WASP_NUMBERS = array(
    'QUEEN' => array('quantity' => 1, 'life' => 80, 'damage' => 7),
    'WORKER' => array('quantity' => 5, 'life' => 68, 'damage' => 10),
    'DRONE' => array('quantity' => 8, 'life' => 60, 'damage' => 12)
);

const WASP_IMAGE = array(
    'QUEEN' => 'img/queen.png',
    'WORKER' => 'img/worker.png',
    "DRONE" => 'img/drone.png'
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
        echo '<div class="container">';
        $last_type = '';
        foreach ($this->wasp_nest as $wasp) {
            $style = "";
            if (isset($_SESSION['wasp_hit']) && spl_object_hash($wasp) == $_SESSION['wasp_hit'] ) {
                $style = 'style="background-color:rosybrown; color:white"';
            }

            if($wasp->get_type() == "QUEEN" && $last_type != "QUEEN") {echo '<div class="row">'; $last_type="QUEEN";}
            if($wasp->get_type() == "WORKER" && $last_type != "WORKER") {echo '</div><div class="row float-left">';$last_type="WORKER";}
            if($wasp->get_type() == "DRONE" && $last_type != "DRONE") {echo '</div><div class="row float-left">'; $last_type="DRONE";}
            if (!isset($_SESSION['queen_dead'])) {

                echo '<div ' . $style . ' class="card ' . $wasp->get_type() . ' wasp-alive-' . $wasp->isAlive() . '">
                    <div class="card-img-top"><img src="' . $image = WASP_IMAGE[$wasp->get_type()] . '"></div>
                    <div class="card-title"><h3>' . $wasp->get_type() . '</h3></div>
                    <div class="life-status">Remaining Health :' . $wasp->get_life() . '</div>
                    
                </div>';
            }
        }
        echo '</div></div>';
        if (isset($_SESSION['queen_dead'])){
            echo '  <h2>You have killed the swarm</h2>
                    <form action="index.php" method="post">
                        <input class="btn" type="submit" value="Start Next Swarm" name="new_game">
                    </form>';
            session_destroy();
        }
        else{ ?>
        <form class="" action="index.php" method="post">
                        <input class="btn" type="submit" value="Hit Random Wasp" name="swat">
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
        if($this->life < $this->damage){
            $this->life = 0;
        } else {
            $this->life -= $this->damage;
            $_SESSION['wasp_hit'] = spl_object_hash($this);
        }
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
        <head>
            <title>NewIcon - Wasp Styled Game</title>
            <style>
                @media (max-width: 769px){
                    .container{
                        width: 100%;
                    }
                    .card {
                        width 50%;
                    }
                }
                body {
                    background-color: #efefef;
                }
                .container {
                    width: 80%;
                    margin: 0 auto;
                    margin-top: 12px ;
                }
                .float-left .card {
                    float: left;
                }
                form{
                    clear: both;
                    text-align: center;
                    padding-top: 12px;
                }
                .card {
                    margin: 2px auto;
                    width: 20%;
                    text-align: center;
                }
                .card .card-title {
                    padding: 5px;
                }
                .card img {
                    width: 30%;
                }
                .btn {
                    border-radius: 5px;
                    box-shadow: -5px 5px 10px 0px #333;
                    background-color: #2e71aa;
                    border: none;
                    color: white;
                    padding: 16px 32px;
                    text-decoration: none;
                    margin: 4px 2px;
                    cursor: pointer;
                    font-size: 1.2rem;
                }
                .btn:hover {
                    padding: 22px 38px;
                    font-size: 1.2rem;
                }
                h2 {
                    clear: both;
                    text-align: center;
                    margin-top: 8px;
                }

                .wasp-alive- .card-title{
                    color: red;
                }
                .wasp-alive- .life-status{
                    color: red;
                }


            </style>
        </head>
        <body>
            <?php $_SESSION["game_engine"]->render_game_state(); ?>

        </body>
    </html>

