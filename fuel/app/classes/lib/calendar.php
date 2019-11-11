<?php

/**
 * class Calendar - support functions for calendar
 *
 * @package Lib
 * @created 2015-03-31
 * @version 1.0
 * @author Le Tuan Tu
 * @copyright Oceanize INC
 */

namespace Lib;

class Calendar {

    /**
     * Get info calendar of date
     *
     * @author Le Tuan Tu
     * @param array $date Input data
     * @return array Info Calendar
     */
    public static function get_calendar($date = null) {
        $date = !empty($date) ? $date : strtotime(date('Y-m-d'));
        $day = date('d', $date);
        $month = date('m', $date);
        $year = date('Y', $date);
        $dayOfWeek = date('D', $date);
        $daysInMonth = cal_days_in_month(0, $month, $year);
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $lastDayOfMonth = strtotime('+' . $daysInMonth - 1 . 'days', $firstDayOfMonth);
        $dayOfWeekByFirstDay = date('D', $firstDayOfMonth);
        $dayOfWeekByLastDay = date('D', $lastDayOfMonth);
        $blankBefore = 0;
        $blankAfter = 0;
        switch ($dayOfWeekByFirstDay) {
            case "Sun":
                $blankBefore = 0;
                break;
            case "Mon":
                $blankBefore = 1;
                break;
            case "Tue":
                $blankBefore = 2;
                break;
            case "Wed":
                $blankBefore = 3;
                break;
            case "Thu":
                $blankBefore = 4;
                break;
            case "Fri":
                $blankBefore = 5;
                break;
            case "Sat":
                $blankBefore = 6;
                break;
        }
        switch ($dayOfWeekByLastDay) {
            case "Sun":
                $blankAfter = 6;
                break;
            case "Mon":
                $blankAfter = 5;
                break;
            case "Tue":
                $blankAfter = 4;
                break;
            case "Wed":
                $blankAfter = 3;
                break;
            case "Thu":
                $blankAfter = 2;
                break;
            case "Fri":
                $blankAfter = 1;
                break;
            case "Sat":
                $blankAfter = 0;
                break;
        }
        $firstDayOfCalendar = strtotime('-' . $blankBefore . 'days', $firstDayOfMonth);
        $lastDayOfCalendar = strtotime('+' . $blankAfter . 'days', $lastDayOfMonth);
        return array(
            'date' => $date,
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'dayOfWeek' => $dayOfWeek,
            'daysInMonth' => $daysInMonth,
            'firstDayOfMonth' => date('Y-m-d', $firstDayOfMonth),
            'lastDayOfMonth' => date('Y-m-d', $lastDayOfMonth),
            'dayOfWeekByFirstDay' => $dayOfWeekByFirstDay,
            'dayOfWeekByLastDay' => $dayOfWeekByLastDay,
            'daysInCalendar' => ($lastDayOfCalendar - $firstDayOfCalendar) / 86400 + 1,
            'firstDayOfCalendar' => date('Y-m-d', $firstDayOfCalendar),
            'lastDayOfCalendar' => date('Y-m-d', $lastDayOfCalendar)
        );
    }

    /**
     * Get day in Japan
     * 
     * @param string|integer $date
     * @return string
     */
    public static function convertDayJapan($date, $language_type = 1) {
        if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        $day = date('D', $date);

        $lang_map = array(
            1 => array('Mon' => '月', 'Tue' => '火', 'Wed' => '水', 'Thu' => '木', 'Fri' => '金', 'Sat' => '土', 'Sun' => '日'),
            2 => array('Mon' => 'Mon', 'Tue' => 'Tue', 'Wed' => 'Wed', 'Thu' => 'Thu', 'Fri' => 'Fri', 'Sat' => 'Sat', 'Sun' => 'Sun'),
            5 => array('Mon' => 'Lu', 'Tue' => 'Ma', 'Wed' => 'Mi', 'Thu' => 'Ju', 'Fri' => 'Vi', 'Sat' => 'Sa', 'Sun' => 'Do'),
        );

        return !empty($lang_map[$language_type][$day]) ? $lang_map[$language_type][$day] : $day;
    }

}
