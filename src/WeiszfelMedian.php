<?php

namespace WeiszfelMedian;

class WeiszfelMedian
{
    /**
     * Algorithm core method
     * @param array $dataArray
     * @param array $previousEstimate
     * @return array
     */
    static public function weiszfeld($dataArray, $previousEstimate)
    {
        $numerator = [0];
        $denominator = 0;
        $keys = array_keys($dataArray[0]);
        foreach ($dataArray as $dataPoint) {
            $numerator = self::addArray(
                $numerator, self::divideArray(
                $dataPoint, self::vectorNorm(
                self::subtractArray($dataPoint, $previousEstimate)
            )
            )
            );
        }

        foreach ($dataArray as $dataPoint) {
            $denominator = $denominator + 1 / (self::vectorNorm(self::subtractArray($dataPoint, $previousEstimate)));
        }

        $median = self::divideArray($numerator, $denominator);

        $median = array_combine($keys, $median);
        return $median;
    }

    /**
     * Wrapper for an iterative call to weiszfeld
     * @param array $dataArray
     * @param int $iterations
     * @param int $decimalPlaces
     * @return array mixed
     */
    static public function getMedian($dataArray, $iterations, $decimalPlaces = 6)
    {
        $median = self::weiszfeld($dataArray, [0, 0, 0, 0, 0, 0]);
        for ($i = 0; $i < $iterations; $i++) {
            $median = self::weiszfeld($dataArray, $median);
        }

        $outputMedian = array_map(
            function ($value) use ($decimalPlaces) {
                return round($value, $decimalPlaces);
            },
            $median
        );
        return $outputMedian;
    }
    /**
     * Adds an array from another array (adds the values at identical keys together)
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    static protected function addArray($arr1, $arr2)
    {
        foreach (array_intersect_key($arr2, $arr1) as $key => $val) {
            $arr1[$key] += $val;
        }
        $arr1 += $arr2;
        return $arr1;
    }

    /**
     * Subtract an array from another array
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    static protected function subtractArray($arr1, $arr2)
    {
        foreach ($arr1 as $key => &$val) {
            if (isset($arr2[$key])) {
                $val -= $arr2[$key];
            }
        }
        return $arr1;
    }

    /**
     * Normalize a vector
     * @param array $array
     * @return float
     */
    static protected function vectorNorm($array)
    {
        $squareSum = 0;
        foreach ($array as $value) {
            $squareSum += pow($value, 2);
        }
        $norm = sqrt($squareSum);
        return $norm;
    }

    /**
     * Multiplies all items in an array by a scalar value
     * @param array $arr
     * @param int $scalar
     * @return array
     */
    static protected function multiplyArray($arr, $scalar)
    {
        return array_map(
            function ($val, $factor) {
                return $val * $factor;
            },
            $arr,
            array_fill(0, count($arr), $scalar)
        );
    }

    /**
     * Divides all items in an array by a scalar value
     * @param array $arr
     * @param int $scalar
     * @return array
     */
    static protected function divideArray($arr, $scalar)
    {
        return array_map(
            function ($val, $factor) {
                return $val / $factor;
            },
            $arr,
            array_fill(0, count($arr), $scalar)
        );
    }
}

