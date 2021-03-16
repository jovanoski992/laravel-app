<?php

namespace App\Http\Controllers;
use \Illuminate\Http\Request;
use App\Activity;
USE DB;

class ScheduleController extends Controller 
{
    public function generateSchedule($startDate) 
    {   
        $getActivities = Activity::all();
        $activitiesArray = [];
        if (!empty($getActivities)) {
            foreach ($getActivities as $activity) {
                $activitiesArray[] = [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'duration' => $activity->duration
                ]; 
            }       
        }

        // next 3 months
        $duration = 90;
        // define empty array
        $lastWorkingDaysDates = [];

        // find lastWorkingDays for next 3 months
        for ($i = 0; $i < 3; $i++) {
            $checkDate = date('Y-m-d', strtotime('+'. $i .' month', strtotime($startDate)));
            $lastDateOfMonth = date("Y-m-t", strtotime($checkDate));

            $lastWorkingDaysDates[$i] = $this->detectLastWorkingDay($checkDate, $lastDateOfMonth);
        }

        $schedule = [];
        // get month (in number format)
        $month = date("m", strtotime($startDate));
        // set ref cleaning to 0
        $ref = 0;
        // generate data for next 3 months
        for ($i = 0; $i < $duration; $i++) {
            // day by day 
            $currentDayRow = date('Y-m-d', strtotime('+'. $i .' day', strtotime($startDate)));
            // get month of current date
            $checkMonth = date("m", strtotime($currentDayRow));
            // get date name of current date
            $dayName = date("l", strtotime($currentDayRow));
            
            // set empty values ( we can comment this if we don't want empty rows into CSV file.)
            $schedule[$i]['date'] = $currentDayRow;
            $schedule[$i]['day'] = $dayName;
            $schedule[$i]['activities'] = '';
            $schedule[$i]['duration'] = '';

            // get day date (number format)
            $day = date("j", strtotime($currentDayRow));
            // check if it's Tuesday or Thursday
            if ($dayName == 'Tuesday' || $dayName == 'Thursday') {
                // set default values
                $act = $activitiesArray[0]['name'];
                $timeEst = date('H:i', mktime(0, $activitiesArray[0]['duration']));
                // if ref is not cleaned or if it's first(tuesday/thursday) of the month
                if ($ref === 0 || $day === '1') { 
                    $act = $activitiesArray[0]['name'] . '|' . $activitiesArray[2]['name'];
                    $timeEst = date('H:i', mktime(0, $activitiesArray[0]['duration'] + $activitiesArray[2]['duration']));
                    
                    // increase ref + 1 cos now it's clened
                    $ref++;
                    // change month if 
                    $month = $checkMonth;
                }

                $schedule[$i]['date'] = $currentDayRow;
                $schedule[$i]['day'] = $dayName;
                $schedule[$i]['activities'] = $act;
                $schedule[$i]['duration'] = $timeEst; 
            }
            // if month is passed update initial variable and reset the ref to 0
            if ($month != $checkMonth) {
                $ref = 0;
                $month = $checkMonth;
            }

            // check if it's last working day
            if (in_array($currentDayRow, $lastWorkingDaysDates)) {
                $act = $activitiesArray[1]['name'];
                $timeEst = date('H:i', mktime(0, $activitiesArray[1]['duration']));
                // check if there is any other activity added before
                if (!empty($schedule[$i]['activities'])) {
                    $act = $activitiesArray[0]['name'] . '|' . $activitiesArray[1]['name'];
                    $timeEst = date('H:i', mktime(0, $activitiesArray[0]['duration'] + $activitiesArray[1]['duration']));
                }

                $schedule[$i]['date'] = $currentDayRow;
                $schedule[$i]['day'] = $dayName;
                $schedule[$i]['activities'] = $act;
                $schedule[$i]['duration'] = $timeEst;
            }
        }
        // export CSV
        $this->exportCsvFile($schedule, 'schedule-' . $startDate);
    }

    public function detectLastWorkingDay($date, $lastDateOfMonth) 
    {
        // set default value
        $lastWorkingDay = date('Y-m-d', strtotime($date));
        $lastDayName = date("l",strtotime($lastDateOfMonth));

        // If it's Sunday or Saturday we set Friday as last working day
        $checkLastWorkingDay = $this->getLastWorkingDayDate($lastDayName, $lastDateOfMonth);
        if (!empty($checkLastWorkingDay)) {
            $lastWorkingDay = $checkLastWorkingDay; 
        }

        return $lastWorkingDay;
    }

    public function getLastWorkingDayDate($name, $lastDateOfMonth) 
    {
        switch ($name) {
            case 'Sunday': 
                $lastWorkingDay = date('Y-m-d', strtotime('-2 day', strtotime($lastDateOfMonth)));
                break;
            case 'Saturday': 
                $lastWorkingDay = date('Y-m-d', strtotime('-1 day', strtotime($lastDateOfMonth)));
                break;
            default: 
            $lastWorkingDay = $lastDateOfMonth;
        }

        return $lastWorkingDay;
    }
    
    public function exportCsvFile($data, $filename) 
    {
        header('Content-Type: text/csv;');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        header("Pragma: no-cache");
        header("Expires: 0");
        $fh = fopen('./csv/'. $filename . '.csv' , 'w');
        fputcsv($fh, array('Date','Day','Activities','Duration(HH:MM)'));
        foreach ($data as $row) {
            fputcsv($fh, array($row['date'],$row['day'],$row['activities'],$row['duration']));
        }
    }
}

