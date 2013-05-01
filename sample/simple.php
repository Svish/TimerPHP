<?php

include "../src/Timer.php";

function wait_seconds($x)
{
    Timer::start(__METHOD__, func_get_args());
    // Slow work
    sleep($x);
    Timer::stop();
}


Timer::start("timer_example.php");
sleep(1);
wait_seconds(3);

header('content-type: text/plain; charset=utf-8');
echo Timer::result();
