<?php

    class SlowMath
    {
        public function slowAdd($x, $y)
        {
            // Start a new timer for this method
            // If a timer is already running, which it is
            // in this case, this timer will be added as a child to it
            Timer::start(__METHOD__, func_get_args());

            // Do work
            sleep(4);
            $r = $x + $y;

            // Stop the timer we started for this method
            Timer::stop();

            // Return result
            return $r;
        }
    }

    // If you use composer autoloading, you shouldn't need this
    include '../src/Timer.php';
    
    // Start our main timer
    Timer::start($_SERVER['PHP_SELF'], $_GET);

    // Timer output uses some box drawings, 
    // so utf-8 should be used (which it should be anyways)
    header('content-type: text/plain; charset:utf-8');

    // Do some stuff taking time
    $math = new SlowMath();
    $math->slowAdd($_GET['x'], $_GET['y']);
    sleep(1);

    // null, true and false parameters will be replaced
    // with a string representation when using __toString
    Timer::start('test', array(null, true, false));
    sleep(2);
    Timer::stop();

    // Stop all still running timers and print the result
    echo Timer::result();