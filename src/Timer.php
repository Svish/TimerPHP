<?php

/**
 * Simple Timer class for logging time and memory usage of methods.
 *
 * @link https://github.com/Svish/TimerPHP
 */
class Timer
{
	/**
	 * Name of this Timer. Usually a function name.
	 */
	public $name;

	/**
	 * Usually the parameters of the function, 
	 * but can also be something else extra to identify this Timer.
	 */
	public $parameters;

	/**
	 * Time elapsed between start and stop in seconds.
	 */
	public $time;

	/**
	 * Delta of memory usage between start and stop.
	 */
	public $memory;

	/**
	 * Peak memory usage after stop.
	 */
	public $memory_peak;
	
	/**
	 * Timers started while this Timer was running.
	 */
	public $timers = array();


	/**
	 * Returns a text representation of the Timer data.
	 */
	public function __toString()
	{
		ob_start();
		$this->printStats();
		return ob_get_clean();
	}
	

	private function __construct($name, array $parameters)
	{
		$this->name = $name;
		$this->time = microtime(TRUE);
		$this->parameters = $parameters;
		$this->memory = memory_get_usage();
	}

	private function end()
	{
		$this->time = microtime(TRUE) - $this->time;
		$this->memory = memory_get_usage() - $this->memory;
		$this->memory_peak = memory_get_peak_usage();
		return $this;
	}

	private function printStats($level = 0)
	{
		$parameters = $this->parameters;

		foreach($parameters as &$p)
			if($p === NULL)
				$p = 'null';
			elseif($p === TRUE)
				$p = 'true';
			elseif($p === FALSE)
				$p = 'false';


		echo $level == 0
			? $this->name
			: str_repeat(' │ ', $level-1).' ├ '.$this->name;

		echo '('.implode(', ', $parameters).')'."\r\n";

		$level += 1;

		echo str_repeat(' │ ', $level)."\r\n";
		echo str_repeat(' │ ', $level).number_format($this->time, 3)." s\r\n";
		echo str_repeat(' │ ', $level).self::bytes($this->memory).', '.self::bytes($this->memory_peak)."\r\n";
	
		foreach($this->timers as $timer)
		{
			echo str_repeat(' │ ', $level)."\r\n";
			$timer->printStats($level);
			echo str_repeat(' │ ', $level)."─┘ \r\n";
		}
	}


	/**
	 * Array to keep track of what level we are on with Timers inside Timers.
	 */
	private static $level = array();

	/**
	 * Starts a new Timer.
	 * 
	 * Will be added as a sub timer to currently running Timer, if any.
	 * Otherwise this will be the new "top" timer.
	 *
	 * @param $name Name of a method or block of code.
	 * @param $parameters Parameters of the method if wanted in the log.
	 * @return The new Timer.
	 */
	public static function start($name, array $parameters = array())
	{
		$t = new Timer($name, $parameters);

		if( ! empty(self::$level))
			array_push(end(self::$level)->timers, $t);

		array_push(self::$level, $t);
		return $t;
	}

	/**
	 * Stops and returns the current Timer.
	 *
	 * @return Stopped Timer.
	 */
	public static function stop()
	{
		return array_pop(self::$level)->end();
	}

	/**
	 * Stops still running Timers and returns the last, "top" one.
	 *
	 * @return First Timer created.
	 */
	public static function result()
	{
		while( ! empty(self::$level))
			$last = self::stop();
		return $last;
	}


	private static function bytes($bytes)
	{
		$symbols = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');

		if($bytes == 0)
			return sprintf('%.2f '.$symbols[0], 0);

		$exp = floor(log(abs($bytes)) / log(1024));
		return sprintf('%.2f '.$symbols[$exp], $bytes/pow(1024, floor($exp)));
	}
}