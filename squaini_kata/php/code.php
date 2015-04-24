<?php

require "vendor/autoload.php";

class AutoTest extends \PHPUnit_Framework_TestCase
{
    public function testMove()
    {
        $auto = new Auto(1, 5);
        $auto->move();
        $this->assertGreaterThan(0, $auto->position);
        $this->assertLessThan(5, $auto->position);
    }
}

class CircuitoTest extends \PHPUnit_Framework_TestCase
{

}

class Auto
{
    public $id;
    public $position;
    public $maxSpeed;

    public function __construct($id, $maxSpeed)
    {
        $this->id = $id;
        $this->position = 0;
        $this->maxSpeed = $maxSpeed;
    }

    public function move()
    {
        $speed = rand(1, $this->maxSpeed);
        $this->position += $speed;
        return $this;
    }

    public function display($isWinner = false)
    {
        return str_repeat("-", $this->position - 1)."X".($isWinner ? " WINNER !!!" : "");
    }
}

class Circuito
{
    private $autos;
    private $turns;

    public function __construct($turns, array $autos)
    {
        $this->turns = $turns;
        $this->autos = $autos;
    }

    public function doTurn()
    {
        array_map(function ($auto) {
            /** @var Auto $auto */
            $auto->move();
        }, $this->autos);
    }

    public function race()
    {
        print chr(27) . "[2J" ; // clear screen

        for ($i = 0; $i < $this->turns; $i++) {
            $this->doTurn();
            $this->display($i === $this->turns - 1);
        }
    }

    private function display($isLast = false)
    {
        print chr(27) . "[;H"; // go to top left
        print "\n";

        array_map(function ($auto) use ($isLast) {
            $str = str_pad($auto->id, 3, ' ', STR_PAD_LEFT).") ";
            $str .= $auto->display($isLast && in_array($auto->id, $this->getWinners()));
            print $str."\n";
        }, $this->autos);

        sleep(1);
    }

    public function getWinners()
    {
        return array_reduce($this->autos, function ($carry, $auto) {
            if ($auto->position > $carry[1]) {
                $carry[0] = [$auto->id];
                $carry[1] = $auto->position;
            }
            if ($auto->position === $carry[1]) {
                $carry[0][] = $auto->id;
            }
            return $carry;
        }, [[0], 0])[0];
    }
}

$maxSpeed = 5;

$numAuto = $argv[1];
$numTurns = $argv[2];

$autos = [];
for ($i = 1; $i <= $numAuto; $i++) {
    $autos[] = new Auto($i, $maxSpeed);
}
$race = new Circuito($numTurns, $autos);
$race->race();