<?hh // strict
/**
 * Copyright (c) 2017, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree. An additional
 * grant of patent rights can be found in the PATENTS file in the same
 * directory.
 *
 */

namespace Facebook\HHAST\__Private;

final class PerfCounter {
  private static dict<string, float> $counters = dict[];

  private float $start;
  private ?float $end;

  public function __construct(
    private string $name,
  ) {
    $this->start = microtime(true);
  }

  public function end(): void {
    $end = microtime(true);
    invariant(
      $this->end === null,
      "Can't end a counter twice",
    );
    self::bumpCounter($this->name, $this->start, $end);
    $this->end = $end;
  }

  public function __destruct() {
    invariant(
      $this->end !== null,
      'counter %s destroyed without calling ::end()',
      $this->name,
    );
  }

  private static function bumpCounter(string $name, float $start, float $end): void {
    $count = self::$counters[$name] ?? 0.0;
    self::$counters[$name] = $count + ($end - $start);
  }

  public static function getCounters(): dict<string, float> {
    return self::$counters;
  }
}