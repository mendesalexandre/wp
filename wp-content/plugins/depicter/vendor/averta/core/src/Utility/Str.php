<?php
namespace Averta\Core\Utility;


class Str
{
	/**
	 * Convert string to camelCase
	 *
	 * @param string  $input                     The string to convert
	 * @param string  $separator                 The character that should be removed
	 * @param bool    $capitalizeFirstChar       Whether to capitalize the first character or not
	 *
	 * @return string
	 */
	public static function camelize( $input, $separator = "_", $capitalizeFirstChar = false ) {

		$string = str_replace( $separator, '', ucwords( $input, $separator ) );

		if ( ! $capitalizeFirstChar ) {
			$string = lcfirst( $string );
		}

		return $string;
	}

	/**
	 * Generates and trims a persistent simple hash
	 *
	 * @param string $data   The input string.
	 * @param int    $start  If offset is negative, the returned string will start at the offset'th character from the end of string.
	 * @param int    $length If length is given and is positive, the string returned will contain at most length characters beginning from start.
	 *
	 * @return false|string
	 */
	public static function simpleHash( $data, $start = 0, $length = 10 ){
		return self::hash( $algorithm = 'md5' , $data, $start, $length );
	}

	/**
	 * Generates and trims a hash value
	 *
	 * @param string $algorithm   Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..)
	 * @param string $data        The input string.
	 * @param int    $start       If offset is negative, the returned string will start at the offset'th character from the end of string.
	 * @param int    $length      If length is given and is positive, the string returned will contain at most length characters beginning from start.
	 *
	 * @return false|string
	 */
	public static function hash( $algorithm , $data, $start = 0, $length = 100 ){
		return substr( hash( $algorithm, $data, false ), $start, $length );
	}

	/**
	 * Generates a persistent simple short hash
	 *
	 * @param string $data        The input string.
	 * @param bool   $binary
	 *
	 * @return string
	 */
	public static function shortHash( $data, $binary = false ){
		return hash( 'adler32', $data, $binary );
	}

	/**
	 * Extracts and returns a part of string based on provided regex.
	 *
	 * @param string $string      String to search in by regex
	 * @param string $regex       Regular expression for extracting a part of URL
	 * @param int    $matchIndex  Which part of matched part we are looking for. Set -1 to get all matches.
	 *
	 * @return bool|mixed   Returns the matched part on success, and false on failure.
	 */
	public static function extractByRegex( $string, $regex, $matchIndex = 1 ){

		$matches = [];

		preg_match(
			$regex,
			$string,
			$matches
		);

		if( $matchIndex === -1 ){
			return $matches;
		}

		if( isset( $matches[ $matchIndex ] ) ){
			return $matches[ $matchIndex ];
		}

		return false;
	}

}
