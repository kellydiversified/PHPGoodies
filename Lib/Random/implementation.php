<?php
/**
 * PHPGoodies:Lib_Random - Multi-algorithm random number generator
 *
 * This is an abstraction layer to wrap around multiple random number generation algorithms. At the
 * very least we have PHP's native algorithm and anything else of, say for example ISAAC which is
 * cryptographically secure and reproducible cross-platform as needed to get matching results.
 *
 * @uses Lib_Data_String
 * @uses Lib_Random_Algorithm_Native
 * @uses Lib_Random_Algorithm_Isaac
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

/**
 * Multi-algorithm random number generator
 */
class Lib_Random {

	/*
	 * Available random number algorithms
	 */
	const RANDOM_ALG_NATIVE		= 0; // PHP Native algorithm
	const RANDOM_ALG_ISAAC		= 1; // ISAAC algorithm

	/**
	 * The algorithm selected at instantiation
	 */
	protected $algorithm;

	/**
	 * The instance of the algorithm implementation that we are using
	 */
	protected $randomAlgorithm;

	/**
	 * Constructor
	 *
	 * @param integer $algorithm One of the self::RANDOM_ALG_* constants
	 */
	public function __construct($algorithm = self::RANDOM_ALG_NATIVE) {

		switch ($algorithm) {
			case self::RANDOM_ALG_NATIVE:
				$this->randomAlgorithm = PHPGoodies::instantiate('Lib.Random.Algorithm.Native');
				break;

			case self::RANDOM_ALG_ISAAC:
				$this->randomAlgorithm = PHPGoodies::instantiate('Lib.Random.Algorithm.Isaac');
				break;

			default:
				throw new \Exception("Unknown Random Algorithm '{$algorithm}'");
		}

		$this->algorithm = $algorithm;
	}

	/**
	 * Seed the random number generator algorithm
	 *
	 * For algorithms that are numerically seeded, we use our internal method to convert the
	 * string into a number and seed them with the number generated from the string...
	 *
	 * @param string $seed A secret string to use for the seed
	 */
	public function seed($seed) {
		switch ($this->algorithm) {

			// Numerically seeded algorithms:
			case self::RANDOM_ALG_NATIVE:
				$this->randomAlgorithm->seed($this->stringToNumber($seed));
				break;

			// String-seeded algorithms:
			case self::RANDOM_ALG_ISAAC:
				$this->randomAlgorithm->seed($seed);
				break;

			default:
				throw new \Exception("Algorithm '{$this->algorithm}' not mapped for seeding");
		}
	}

	/**
	 * Get the next random number generated by the algorithm
	 *
	 * @param integer Optional maximum to get a random number from 0 to max
	 *
	 * @return integer The next random number
	 */
	public function rand($max = null) {
		$val = $this->randomAlgorithm->rand();
		if (! is_null($max)) {
			$mod = (integer) $max + 1;
			$val %= $mod;
		}
		return $val;
	}

	/**
	 * Generates a number whose value is "inspired" by the supplied string
	 *
	 * The idea is to take a "secret" string phrase and convert it into a relatively distinctive
	 * number to use as a random number generator's seed (for the algorithms that are seeded
	 * numerically).
	 *
	 * @param string $str The secret string we want to use
	 *
	 * @return integer The generated number inspired by the string
	 */
	protected function stringToNumber($str) {

		// Break secret string into 4-byte chunks (32 bits each)
		$str = PHPGoodies::instantiate('Lib.Data.String', $str);
		$chunks = $str->getChunked(4);
		$num = 0x5AF00FA5; // 01011010111100000000111110100101
		foreach ($chunks as $chunk) {

			// Get the numeric value for this chunk
			$chunkNumeric = 0;
			for ($xx = 0; $xx < strlen($chunk); $xx++) {
				$chunkNumeric <<= 8;
				$chunkNumeric |= ord($chunk{$xx});
			}

			// XOR the value onto the number
			$num ^= $chunkNumeric;
		}

		return $num;
	}
}

