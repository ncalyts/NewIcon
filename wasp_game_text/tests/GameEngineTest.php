<?php
require_once './index.php';
use PHPUnit\Framework\TestCase;

class GameEngineTest extends TestCase
{
    public function testSwarmCreated() {
        $gameEngine = new GameEngine();
        $swarm = $gameEngine->remaining_swarm();
        $this->assertIsArray($swarm);
    }

    public function testSwarmSizeGreaterThan() {
        $gameEngine = new GameEngine();
        $swarm = $gameEngine->remaining_swarm();
        $this->assertTrue(count($swarm) > 0);
    }

    public function testRoleEqualLife() {
        $gameEngine = new GameEngine();
        $swarm = $gameEngine->remaining_swarm();
        $filtered_swarm = array_filter($swarm, function ($wasp) {
            switch ($wasp->get_type()){
                case "QUEEN":
                    $this->assertTrue($wasp->get_life() === 80);
                    break;
                case "WORKER":
                    $this->assertTrue($wasp->get_life() === 68);
                    break;
                default:
                    $this->assertTrue($wasp->get_life() === 60);
                    break;
            }
        });
    }
}
