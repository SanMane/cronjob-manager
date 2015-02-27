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
if(isset($_POST['remove']))
    $msg = $CronObj->remove($_POST['remove']);
$crons = $CronObj->get();
?>
        <title>Cron Jobs</title>
    </head>
    <body>
        <?php include_once 'Includes/header.php'; ?>
        <div class="container">
            <?php if(isset($msg)) echo $msg; ?>
            <p align="center"><a href="cron.php" class="btn btn-success">Add Cron Job</a></p>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <?php
                    if(!count($crons))
                    {
                        ?><tr class="warning"><td align="center">No Cron Jobs to Display!!</td></tr><?php
                    }
                    else
                    {
                        ?>
                        <thead>
                            <tr>
                                <th width="80px"></th>
                                <th>#</th>
                                <th>ID</th>
                                <th>Comment</th>
                                <th>Interval</th>
                                <th>Trigger</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            foreach ($crons as $id=>$cron) {
                                $CronObj->status($cron);
                                ?>
                                <tr>
                                    <td align="center">
                                        <form method="post" action="" onsubmit="return confirmDelete()">
                                            <a href="cron.php?id=<?php echo $id; ?>" class="btn-link" title="Edit"><i class="fa fa-pencil text-default fa-lg"></i></a>
                                            <button type="submit" name="remove" value="<?php echo $id; ?>" class="btn-link" title="Remove"><i class="fa fa-times text-danger fa-lg"></i></button>
                                        </form>
                                    </td>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $cron[8]; ?></td>
                                    <td><?php print_r($cron[9]); ?></td>
                                    <td><?php echo $CronObj->status($cron); ?></td>
                                    <td><?php echo $cron[6]; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </div>
    </body>
</html>
