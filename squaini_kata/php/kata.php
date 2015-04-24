<?php

$totalCars = 5;
$totalTurns = 70;
$maxSpeed = 4;

$race = new Race($totalCars, $totalTurns, $maxSpeed);
$race->start();

/**
 * Class Race
 */
class Race
{
    /**
     * @var int
     */
    private $totalCars;

    /**
     * @var int
     */
    private $totalTurns;

    /**
     * @var int
     */
    private $maxSpeed;

    /**
     * @var array
     */
    private $racers;

    /**
     * @var
     */
    private $roadLength;

    /**
     * @param int $totalCars
     * @param int $totalTurns
     * @param int $maxSpeed
     */
    public function __construct($totalCars = 5, $totalTurns = 50, $maxSpeed = 3)
    {
        $this->totalCars = $totalCars;
        $this->totalTurns = $totalTurns;
        $this->maxSpeed = $maxSpeed;
        $this->racers = [];
    }

    public function start()
    {
        $this->prepareRace();
        $this->readySetGo();
        $this->renderRace();
        $this->finishRace();
    }

    protected function prepareRace()
    {
        for ($i = 1; $i <= $this->totalCars; $i++) {
            $racer = new Racer($i, $this->totalTurns, $this->maxSpeed);
            $racer->prepare();
            $this->racers[] = $racer;
        }

        $this->roadLength = $this->getWinner()->getTotalRoad();
    }

    /**
     * @param $message
     */
    protected function clearAndMessage($message)
    {
        echo sprintf("\033c\n%s \n", $message);
    }

    protected function readySetGo()
    {
        for ($time = 3; $time > 0; $time--) {
            $this->clearAndMessage(" $time ...");
            foreach ($this->racers as $racer) {
                /** @var Racer $racer */
                echo sprintf(
                    "%s %s\n",
                    $racer->getCarDesign(),
                    str_pad("", $this->roadLength, "=")
                );
            }
            sleep(1);
        }
        sleep(1);
    }

    protected function renderRace()
    {
        for ($y = 1; $y <= $this->totalTurns; $y++) {
            $this->clearAndMessage("Go !!!");
            foreach ($this->racers as $racer) {
                /** @var Racer $racer */
                echo sprintf(
                    "%s %s %s\n",
                    str_pad("", $racer->getTotalRoadAtTurn($y), "="),
                    $racer->getCarDesign(),
                    str_pad("", ($this->roadLength - $racer->getTotalRoadAtTurn($y) - 1), "=")
                );
            }
            usleep(100000);
        }
    }

    protected function finishRace()
    {
        $this->clearAndMessage("Finish !!!");
        foreach ($this->racers as $racer) {
            /** @var Racer $racer */
            if ($racer === $this->getWinner()) {
                echo sprintf(
                    "%s \e[42m\e[4m%s\e[24m\e[49m WINNER!!!\n",
                    str_pad("", $racer->getTotalRoadAtTurn($this->totalTurns), "="),
                    $racer->getCarDesign()
                );
            } else {
                echo sprintf(
                    "%s %s %s \n",
                    str_pad("", $racer->getTotalRoadAtTurn($this->totalTurns), "="),
                    $racer->getCarDesign(),
                    str_pad("", ($this->roadLength - $racer->getTotalRoadAtTurn($this->totalTurns) - 1), "=")
                );
            }
        }
    }

    /**
     * @return null|Racer
     */
    protected function getWinner()
    {
        $winner = null;
        foreach ($this->racers as $racer) {
            /** @var Racer $racer */
            if (is_null($winner)) {
                $winner = $racer;
            }
            if ($racer->getTotalRoad() > $winner->getTotalRoad()) {
                $winner = $racer;
            }
        }

        return $winner;
    }
}

/**
 * Class Racer
 */
class Racer
{
    /**
     * @var
     */
    private $number;

    /**
     * @var array
     */
    private $turns;

    /**
     * @var
     */
    private $totalTurns;

    /**
     * @var
     */
    private $totalRoad;

    /**
     * @var
     */
    private $maxSpeed;

    /**
     * @param $number
     * @param $totalTurns
     * @param $maxSpeed
     */
    public function __construct($number, $totalTurns, $maxSpeed)
    {
        $this->number = $number;
        $this->totalTurns = $totalTurns;
        $this->turns = [];
        $this->maxSpeed = $maxSpeed;
    }

    public function prepare()
    {
        for ($y = 1; $y <= $this->totalTurns; $y++) {
            $this->turns[$y] = rand(1, $this->maxSpeed);
        }
        $this->totalRoad = array_sum($this->turns);
    }

    /**
     * @return mixed
     */
    public function getTotalRoad()
    {
        return $this->totalRoad;
    }

    /**
     * @param $turn
     * @return mixed
     */
    public function getRoadAtTurn($turn)
    {
        return $this->turns[$turn];
    }

    /**
     * @param $turn
     * @return int
     */
    public function getTotalRoadAtTurn($turn)
    {
        $sum = 0;
        for ($i = 1; $i <= $turn; $i++) {
            $sum+=$this->turns[$i];
        }

        return $sum;
    }

    /**
     * @return string
     */
    public function getCarDesign()
    {
//        return "8[$this->number]>";
        return "8o8>";
    }
}
