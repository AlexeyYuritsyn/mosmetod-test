<?php
namespace app\commands;

use Yii;
use app\models\Materials;
use yii\console\Controller;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DateUnpinningController extends Controller
{
    public function actionIndex()
    {
        Materials::updateAll(['hits'=>false],'date_unpinning IS NOT NULL AND date_unpinning < :date_unpinning',[':date_unpinning'=>date('Y-m-d H:i:s',time())]);
    }
}
