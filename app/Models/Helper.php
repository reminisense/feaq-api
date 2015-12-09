<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/18/2015
 * Time: 3:25 PM
 */
namespace App\Models;

use \DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Helper extends Model
{
    public static function parseTime($time)
    {
        $arr = explode(' ', $time);
        $hourmin = explode(':', $arr[0]);
        return [
            'hour' => trim($hourmin[0]),
            'min' => trim($hourmin[1]),
            'ampm' => trim($arr[1]),
        ];
    }

    /**
     * gets timezone offset and converts it to php timezone string
     * @param $offset
     * @return bool
     */
    public static function timezoneOffsetToName($offset)
    {
        $abbrarray = timezone_abbreviations_list();
        foreach ($abbrarray as $abbr) {
            foreach ($abbr as $city) {
                if ($city['offset'] == $offset) {
                    return $city['timezone_id'];
                }
            }
        }
        return false;
    }

    public static function timezoneOffsetToNameArray($offset)
    {
        $timezones = [];
        $abbrarray = timezone_abbreviations_list();
        foreach ($abbrarray as $abbr) {
            foreach ($abbr as $city) {
                if ($city['offset'] == $offset) {
                    $timezones[] = $city['timezone_id'];
                }
            }
        }
        return $timezones;
    }

    public static function doubleZero($number)
    {
        return $number == 0 ? '00' : $number;
    }

    public static function changeBusinessTimeTimezone($date, $business_timezone, $browser_timezone)
    {
        $browser_timezone = $browser_timezone != null ? $browser_timezone : $business_timezone;
        if (is_numeric($browser_timezone)) $browser_timezone = Helper::timezoneOffsetToName($browser_timezone);
        $datetime = new \DateTime($date, new \DateTimeZone($business_timezone));
        $datetime->setTimezone(new \DateTimeZone($browser_timezone));
        return $datetime->format('g:i A');
    }

    public static function millisecondsToHMSFormat($ms)
    {
        $second = $ms % 60;
        $ms = floor($ms / 60);
        $minute = $ms % 60;
        $ms = floor($ms / 60);
        $hour = $ms % 24;
        return Helper::formatTime($second, $minute, $hour);
    }

    public static function formatTime($second, $minute, $hour)
    {
        $time_string = '';
        $time_string .= $hour > 0 ? $hour . ' hour(s) ' : '';
        $time_string .= $minute > 0 ? $minute . ' minute(s) ' : '';
        $time_string .= $second > 0 ? $second . ' second(s) ' : '';
        return $time_string;
    }

    /**
     * requires an array of arrays
     * ex. 'field' => array('conditional_operator', 'value')
     * @param $conditions
     * @return mixed
     */
    public static function getMultipleQueries($table, $conditions)
    {
        $query = DB::table($table);
        foreach ($conditions as $field => $value) {
            $field = strpos($field, '.') > 0 ? substr($field, 0, strpos($field, '.')) : $field;
            if (is_array($value)) {
                $query->where($field, $value[0], $value[1]);
            } else {
                $query->where($field, '=', $value);
            }
        }
        return $query->get();
    }

    /**
     * Checks if date string is of Ymd fomat
     * @param $date date string
     * @return is Ymd or not
     */
    public static function is_Ymd($date)
    {
        $d = DateTime::createFromFormat('Ymd', $date);
        return $d && $d->format('Ymd') == $date;
    }
}