<?php
/**
 * @author   Sandip Mane <sandip2490@gmail.com>
 * @version  0.1
 * @package  cronjob-manager
 *
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

class Cron
{
	protected $crontab 	= NULL;
	protected $interval = NULL;

	function __construct()
	{
		require_once 'Libs/Crons/src/CrontabManager.php';
		require_once 'Libs/Crons/src/CronEntry.php';
		$this->crontab = new CrontabManager();
	}
	
	function get()
	{
		try {
			$crons = array();
			$comments = null;
			$jobs = explode("\n", $this->crontab->listJobs());
			if (is_array($jobs)) {
                foreach ($jobs as $oneJob) {
                    if ($oneJob != '')
                    {
                    	if(substr($oneJob, 0, 1)=='#')
                    	{
                    		$comments = $oneJob;
                    		continue;
                    	}

                    	$task = explode(" ", $oneJob);
						$task[0] = explode("\t", $task[0]);
						$crons[$task[3]] = $task[0];
						array_push($crons[$task[3]], $task[1]);
						array_push($crons[$task[3]], $task[2]);
						array_push($crons[$task[3]], $task[3]);
						array_push($crons[$task[3]], str_replace("# ", "", $comments));
						$comments = null;
                    }
                }
            }
			return $crons;
		} catch (UnexpectedValueException $e) {
		    // print_r($e);
		}
	}
	
	function getOne($id)
	{
		try {
			if($this->crontab->jobExists($id))
			{
				$jobs = explode("\n", $this->crontab->listJobs());
				if (is_array($jobs)) {
	                foreach ($jobs as $oneJob) {
	                    if ($oneJob != '') 
	                    {
	                    	if(substr($oneJob, 0, 1)=='#')
	                    	{
	                    		$comments = $oneJob;
	                    		continue;
	                    	}
	                    	$task = explode(" ", $oneJob);
	                    	if($task[3]==$id) {
								$task[0] = explode("\t", $task[0]);
								array_push($task[0], $task[1]);
								array_push($task[0], $task[2]);
								array_push($task[0], $task[3]);
								array_push($task[0], str_replace("# ", "", $comments));
								unset($task[1],$task[2],$task[3],$comments);
								return $task[0];
							}
	                    }
	                }
	            }
			}
			else
				return false;
		} catch (UnexpectedValueException $e) {
		    print_r($e);
		}
	}

	function create($post)
	{
		try {

			$task = "/usr/bin/wget ".$post['service'];
			switch ($post['remRepeat']) {
				case 'month':
					$this->interval = "{$post['minRepeat']} {$post['hourRepeat']} {$post['dayRepeat']} * *";
					break;
				case 'week':
					$this->interval = "{$post['minRepeat']} {$post['hourRepeat']} * * {$post['dayRepeat']}";
					break;
				case 'day':
					$this->interval = "{$post['minRepeat']} {$post['hourRepeat']} * * *";
					break;
				case 'hour':
					if($post['hourRepeat']==0)
						$this->interval = "{$post['minRepeat']} * * * *";
					else
						$this->interval = "{$post['minRepeat']} */{$post['hourRepeat']} * * *";
					break;
				case 'minute':
					$this->interval = "*/{$post['minRepeat']} * * * *";
					break;
			}

			$job = $this->crontab->newJob();
			$job->addComments(array($post['comments']));
			$job->on(" {$this->interval} ")->doJob(" $task ");
			$this->crontab->add($job);
			$this->crontab->save();

        	return BootStrap::MsgSuccess("The Cron has been created.");
		} catch (UnexpectedValueException $e) {
		    return BootStrap::MsgError("There seems some problem with the Data Provided.");
		}
	}
	function update($post)
	{
		try {
			$task = "/usr/bin/wget ".$post['service'];
			switch ($post['remRepeat']) {
				case 'month':
					$this->interval = "{$post['minRepeat']} {$post['hourRepeat']} {$post['dayRepeat']} * *";
					break;
				case 'week':
					$this->interval = "{$post['minRepeat']} {$post['hourRepeat']} * * {$post['dayRepeat']}";
					break;
				case 'day':
					$this->interval = "{$post['minRepeat']} {$post['hourRepeat']} * * *";
					break;
				case 'hour':
					if($post['hourRepeat']==0)
						$this->interval = "{$post['minRepeat']} * * * *";
					else
						$this->interval = "{$post['minRepeat']} */{$post['hourRepeat']} * * *";
					break;
				case 'minute':
					$this->interval = "*/{$post['minRepeat']} * * * *";
					break;
			}

			self::remove($post['id']);

			$job = $this->crontab->newJob();
			$job->addComments(array($post['comments']));
			$job->lineComment = $post['id'];
			$job->on(" {$this->interval} ")->doJob(" $task ");
			$this->crontab->add($job);
			$this->crontab->save();

        	return BootStrap::MsgSuccess("The Cron has been updated.");
		} catch (UnexpectedValueException $e) {
		    return BootStrap::MsgError("There seems some problem with the Data Provided.");
		}
	}
	function status($cron, $bool=FALSE)
	{
		$week 	= $cron[4];
		$month 	= $cron[3];
		$day 	= $cron[2];
		$hour 	= $cron[1];
		$minute = $cron[0];

		if(!$bool)
		{
			// Check if its running per Minute
			if($minute!='*' && $hour=='*')
				return "Running Every ".str_replace("*/","",$minute)." Minutes.";
			// Check if its running Per Hour
			if(($hour=='*' || preg_match("*/*", $hour)) && $minute!='*' && $week=='*' && $month=='*' && $day=='*')
				return "Running Every ".str_replace("*/","",$hour)." Hours at $minute Minutes.";
			// Check if its running Every Day
			if($day=='*' && $week=='*' && $hour!='*' && $minute!='*')
				return "Running Daily at ".date("h:i",strtotime($hour.':'.$minute))." Hours.";
			// Check if its running Monthly
			if($month=='*' && $day!='*' && $hour!='*' && $minute!='*')
				return "Running on Day $day of every Month at ".date("h:i",strtotime($hour.':'.$minute))." Hours.";
			// Check if its running Weekly
			if($week!='*' && $month=='*' && $day=='*' && $hour!='*' && $minute!='*')
				return "Running Weekly, Every ".date('l', strtotime("Sunday + $week Days"))." at ".date("h:i",strtotime($hour.':'.$minute))." Hours.";
		}
		else
		{
			// Check if its running per Minute
			if($minute!='*' && $hour=='*')
				return "minute";
			// Check if its running Per Hour
			if(($hour=='*' || preg_match("*/*", $hour)) && $minute!='*' && $week=='*' && $month=='*' && $day=='*')
				return "hour";
			// Check if its running Every Day
			if($day=='*' && $week=='*' && $hour!='*' && $minute!='*')
				return "day";
			// Check if its running Monthly
			if($month=='*' && $day!='*' && $hour!='*' && $minute!='*')
				return "month";
			// Check if its running Weekly
			if($week!='*' && $month=='*' && $day=='*' && $hour!='*' && $minute!='*')
				return "week";
		}
	}

	function remove($job)
	{
		try {
			$this->crontab->deleteJob($job);
			$this->crontab->save(false);
        	return BootStrap::MsgSuccess("The Cron has been deleted.");
		} catch (UnexpectedValueException $e) {
		    return BootStrap::MsgError("There seems some problem with the CronJob Manager.");
		}
	}
}
