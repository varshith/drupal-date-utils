<?php

namespace Drupal\date_utils;

/**
 * DateUtils manager class.
 */
class DateUtils {

  /**
   * An array containing the number of days in all months.
   *
   * @var array|int[]
   */
  protected array $months = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

  /**
   * Calculates the number of days between two date strings.
   *
   * @param int $start
   *   The start timestamp.
   * @param int $end
   *   The end timestamp.
   * @return int
   *   The number of days between start and end timestamps.
   */
  public function DateNumDays($start, $end) {
    // Assume date string is of format yyyy-mm-dd.
    $start_date_parts = explode('-', $start);
    $end_date_parts = explode('-', $end);

    $start_day = $start_date_parts[2];
    $start_month = $start_date_parts[1];
    $start_year = $start_date_parts[0];
    $end_day = $end_date_parts[2];
    $end_month = $end_date_parts[1];
    $end_year = $end_date_parts[0];

    $result = 0;

    // Rest of days in start-date year.
    for ($i = (int)$start_month; $i <= 12; $i++) {
      if ($this->isLeapYear($start_year) && $i == 2) {
        $result++;
      }
      $result += $this->months[$i];
    }

    // Deduct the number of days from start date.
    $result += $this->months[$start_month] - $start_day;

    // Current year is handled already; increment it.
    $start_year++;

    // Handle full years.
    for ($i = $start_year; $i < $end_year; $i++) {
      if ($this->isLeapYear($i)) {
        $result++;
      }
      $result += 365;
    }

    // Handle days in the end date year.
    if ($end_year >= $start_year) {
      for ($i = 0; $i < $end_month; $i++) {
        if ($this->isLeapYear($end_year) && $i == 2) {
          $result++;
        }
        $result += $this->months[$i];
      }
    }

    // Add the days in the end date.
    $result += $end_day;

    return $result;
  }

  /**
   * Returns true if given year is a leap year.
   *
   * @param int $year
   *   The year.
   * @return bool
   */
  protected function isLeapYear($year) {
    return !($year % 4);
  }
}
