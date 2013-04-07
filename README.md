Introduction
===

Simple class for logging time and memory usage of methods and such.

The `Timer::__toString()` method will create a simple plain text overview,
but you can of course also create your own output by using the properties 
of the Timer objects directly.

Example usage
---

    class SlowMath
    {
        public function slowAdd($x, $y)
        {
            // Start a new timer for this method
            // If a timer is already running, which it is
            // in this case, this timer will be added as a child to it
            Timer::start(__METHOD__, func_get_args());

            // Do work
            sleep(2);
            $r = $x + $y;

            // Stop the timer we started for this method
            Timer::stop();

            // Return result
            return $r;
        }
    }

    include '../src/Timer.php';

    // Start our main timer
    Timer::start('add', $_GET);

    // Timer output uses some box drawings, 
    // so utf-8 should be used (which it should be anyways)
    header('content-type: text/plain; charset:utf-8');

    // Do some stuff taking time
    $math = new SlowMath();
    echo $math->slowAdd($_GET['x'], $_GET['y']);
    sleep(1);
    echo "\r\n\r\n";

    // Stop all still running timers and print the result
    echo Timer::result();


License
===

This work is licensed under the Creative Commons Attribution 3.0 Unported License. To view a copy of this license, visit [Creative Commons Attribution 3.0 Unported License](http://creativecommons.org/licenses/by/3.0/).

![Creative Commons License](http://i.creativecommons.org/l/by/3.0/88x31.png)
