<?php

namespace App\Services\Todo;

use App\Models\Todo\Todo;
use App\Services\BasicDataService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TodoService extends BasicDataService
{
    /**
    *
    * @return Builder
    */
    protected function getModelBuilder(): Builder
    {
        return Todo::query();
    }

    public function getBurndown($user) {
        $timeArray = array();
        $currentTime = strtotime(date('Y-m-d H:i:00'));
        $hourAgo = date('Y-m-d H:i:s', $currentTime - 3600);

        //uncompleted task
        $query = Todo::query();
        $query->where('finished_at', NULL);
        $query->where('user_id', $user);
        $sumUncompleted = $query->count();

        // numbers of task that completed in last hour
        $query = Todo::query();
        $query->select(DB::raw("DATE_FORMAT(finished_at, '%Y-%m-%d %H:%i:00') AS min_time, count(*) as task_count"));
        $query->where('finished_at', '>=', $hourAgo);
        $query->where('user_id', $user);
        $query->groupBy('min_time');
        $query->orderBy('min_time');
        $rows = $query->get();
        $arrayCompleted = empty($rows) ? [] : $rows->toArray();

        // numbers of task that created in last hour
        $query = Todo::query();
        $query->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:00') AS min_time, count(*) as task_count"));
        $query->where('created_at', '>=', $hourAgo);
        $query->where('user_id', $user);
        $query->groupBy('min_time');
        $query->orderBy('min_time');
        $rows = $query->get();
        $arrayCreated = empty($rows) ? [] : $rows->toArray();

        foreach ($arrayCompleted as $value) {
            $ts = strtotime($value['min_time']);
            if (isset($timeArray[$ts])) {
                $timeArray[$ts] += $value['task_count'];
            } else {
                $timeArray[$ts] = $value['task_count'];
            }
        }

        foreach ($arrayCreated as $value) {
            $ts = strtotime($value['min_time']);
            if (isset($timeArray[$ts])) {
                $timeArray[$ts] -= $value['task_count'];
            } else {
                $timeArray[$ts] = -$value['task_count'];
            }
        }

        $xAxis = array();
        $chartData = array();
        $timeFlag = $currentTime;
        $taskFlag = $sumUncompleted;
        for ($x = 0; $x < 60; $x++) {
            $xAxis[] = date('H:i', $timeFlag);
            $chartData[] = $taskFlag;
            if (isset($timeArray[$timeFlag])) {
                $taskFlag += $timeArray[$timeFlag];
            }
            $timeFlag -= 60;
        }

        return $this->result(true, '', ['xAxis' => array_reverse($xAxis), 'chartData' => array_reverse($chartData)]);
    }
}
