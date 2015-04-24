<?php

require_once __DIR__.'/vendor/autoload.php';

use Functional as F;

function clear_screen() {
    print chr(27) . "[2J" ; // clear screen
    print chr(27) . "[;H"; // go to top left
}

function turn($speed) {
    return function ($car, $i, $c, $reduction) use ($speed) {
        $car[] = rand(1, $speed);
        $reduction[] = $car;
        return $reduction;
    };
}

function race($speed, $cars, $turns) {
    clear_screen();
    $cars = array_pad([], $cars, []);
    for ($i = 1; $i <= $turns; $i++) {
        $cars = F\reduce_left($cars, turn($speed), []);
        results($cars, $i === $turns);
    }
}

function results($cars, $finish) {
    print chr(27) . "[;H\n"; // go to top left
    foreach ($cars as $car) {
        print str_repeat('-', array_sum($car) - 1)."[()]";
        print ($finish && is_max($car, $cars) ? ' WINNER!!!' : '')."\n";
    }
    usleep(50000);
}

function is_max($car, $cars) {
    $maxes = F\map($cars, function ($c) {
        return array_sum($c);
    });
    return F\every($maxes, function ($max) use ($car) {
        return $max <= array_sum($car);
    });
}

race((int) $argv[1] ?: 5, (int) $argv[2] ?: 5, (int) $argv[3] ?: 5);





