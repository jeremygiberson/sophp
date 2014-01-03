<?php
namespace SOPHP\Sample\Calculator;

interface CalculatorInterface
{
    /**
     * Compute the sum of two numbers, augend+addend
     * @param float $augend
     * @param float $addend
     * @return float sum
     */
    public function add($augend, $addend);

    /**
     * Compute the product of two numbers, multiplicand*multiplier
     * @param float $multiplicand
     * @param float $multiplier
     * @return mixed
     */
    public function multiply($multiplicand, $multiplier);

    /**
     * Compute the quotient of two numbers, dividend/divisor
     * @param float $dividend
     * @param float $divisor
     * @return float
     */
    public function divide($dividend, $divisor);

    /**
     * Compute the Exponentiation of base ^ exponent.
     * Types are purposely different to test behaviour of Smd/Proxy gen
     * @param float $base
     * @param $exponent
     * @return number
     */
    public function power($base, $exponent);

    /**
     * Compute the $nth root(degree) of a number(radicand), radicand ^ (1/degree);
     * @param float $radicand
     * @param int|float $degree
     * @return float
     */
    public function root($radicand, $degree = 2.0);

    /**
     * Compute the difference of two numbers, minuend-subtrahend
     * @param float $minuend the first number
     * @param float $subtrahend number subtracted from the first number
     * @return float difference
     */
    public function subtract($minuend, $subtrahend);
}