<?PHP
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
include_once 'Includes/master.php';

$CronObj = new Cron();
if(isset($_GET['id']))
{
    $cron = $CronObj->getOne($_GET['id']);
    $status = $CronObj->status($cron,TRUE);
    $day = str_replace("*/","",$cron[2]); 
    $hour = str_replace("*/","",$cron[1]); 
    $min = str_replace("*/","",$cron[0]); 
}

if(isset($_POST['create']))
    $msg = $CronObj->create($_POST);
if(isset($_POST['update']))
    $msg = $CronObj->update($_POST);
?>
        <title>Manage Cron</title>
    </head>
    <body>
        <?php include_once 'Includes/header.php'; ?>
        <div class="container">
            <?php if(isset($msg)) echo $msg; ?>
            <form class="form" method="post" action="">
                <fieldset>
                    <legend><?php if(isset($cron)) echo "Manage CronJob!"; else echo "Create CronJob!"; ?></legend>
                    <div class="form-group col-md-3">
                        <label for="id" class="control-label">Cron ID</label>
                        <input type="text" class="form-control" name="id" id="id" placeholder="Cron ID" readonly value="<?php if(isset($cron)) echo $cron[8]; ?>" required>
                    </div>
                    <div class="form-group col-md-9">
                        <label for="service" class="control-label">Trigger URL</label>
                        <input type="text" class="form-control" name="service" id="service" placeholder="Trigger URL" value="<?php if(isset($cron)) echo $cron[6]; ?>" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="comments" class="control-label">Comments</label>
                        <input type="text" class="form-control" name="comments" id="comments" placeholder="Comments" value="<?php if(isset($cron)) echo $cron[9]; ?>" required>
                    </div>
                    <legend></legend>
                    <div class="form-group col-md-6">
                        <label for="remRepeat" class="control-label">Reminder Interval</label>
                        <select name="remRepeat" id="remRepeat" class="form-control" required>
                            <option value="">Select Interval</option>
                            <option value="month" <?php if(isset($status) && $status=='month') echo 'selected'; ?>>Month</option>
                            <option value="week" <?php if(isset($status) && $status=='week') echo 'selected'; ?>>Week</option>
                            <option value="day" <?php if(isset($status) && $status=='day') echo 'selected'; ?>>Day</option>
                            <option value="hour" <?php if(isset($status) && $status=='hour') echo 'selected'; ?>>Hour</option>
                            <option value="minute" <?php if(isset($status) && $status=='minute') echo 'selected'; ?>>Minute</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="dayRepeat" class="control-label">Reminder Interval Day</label>
                        <select name="dayRepeat" id="dayRepeat" class="form-control" required>
                            <?php 
                            for ($i=0; $i <= 31 ; $i++) { 
                                ?><option value="<?php echo $i; ?>" <?php if(isset($status) && $day==$i) echo ' selected'; ?>><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option> <?php
                            }
                            ?>
                        </select>
                        <p class="help-block"><b>Monthly:</b> Chose date to trigger the action every month.</p>
                        <p class="help-block"><b>Weekly:</b> Chose Number of day of the week to trigger the action, use from 0-6 (i.e. 0-Sunday, 1-Monday ..., 6-Saturday)</p>
                        <p class="help-block"><b>Daily, Hourly, Minutely:</b> Put it Zero(It doesnt matter even if you don't).</p>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="hourRepeat" class="control-label">Reminder Interval Hours</label>
                        <select name="hourRepeat" id="hourRepeat" class="form-control" required>
                            <?php 
                            for ($i=0; $i <= 24 ; $i++) { 
                                ?><option value="<?php echo $i; ?>" <?php if(isset($status) && $hour==$i) echo ' selected'; ?>><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option> <?php
                            }
                            ?>
                        </select>
                        <p class="help-block"><b>Monthly, Weekly, Daily:</b> Chose Time in Hours - 24Hr format (i.e. 11 for 11 AM, 17 for 5 PM etc...)</p>
                        <p class="help-block"><b>Hourly:</b> Chose Number of hours to recur the task (i.e. 0,1-every hour, 2- every 2 hours etc...)</p>
                        <p class="help-block"><b>Minutely:</b> Put it Zero(It doesnt matter even if you don't).</p>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="minRepeat" class="control-label">Reminder Interval Minutes</label>
                        <select name="minRepeat" id="minRepeat" class="form-control" required>
                            <?php
                            for ($i=0; $i <= 60 ; $i++) { 
                                ?><option value="<?php echo $i; ?>" <?php if(isset($status) && $min==$i) echo ' selected'; ?>><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option> <?php
                            }
                            ?>
                        </select>
                        <p class="help-block"><b>Monthly, Weekly, Daily, Hourly:</b> Chose Time in Minutes.</p>
                        <p class="help-block"><b>Minutely:</b> Chose minutes>0 to recure the task (i.e. 1-Every Minute, 2-Every two minutes etc...).</p>
                    </div>
                    <div class="form-group col-md-3 col-lg-offset-9">
                        <a class="btn btn-warning" href="index.php">Cancel</a>
                        <button type="submit" name="<?php if(isset($cron)) echo "update"; else echo "create"; ?>" class="btn btn-success">
                        <?php if(isset($cron)) echo "Update CronJob!"; else echo "Create CronJob!"; ?>
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
    </body>
</html>
