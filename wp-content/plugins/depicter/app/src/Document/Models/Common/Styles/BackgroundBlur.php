<?php
namespace Depicter\Document\Models\Common\Styles;

use Depicter\Document\CSS\Breakpoints;

class BackgroundBlur extends States
{
	/**
	 * style name
	 */
	const NAME = 'backdrop-filter';

	/**
	 * @var int
	 */
	public $blur = 0;

	/**
	 * @var int
	 */
	public $opacity = 100;

	/**
	 * @var int
	 */
	public  $brightness = 100;

	public function set( $css ) {
		$devices = Breakpoints::names();
		foreach ( $devices as $device ) {
			if ( isset( $this->{$device}->enable ) ) {

				// If it is disabled in a breakpoint other than default, generate a reset style for breakpoint
				if( $device !== 'default' && ! empty( $this->default->enable )  && ! $this->{$device}->enable ) {
					$css[$device][ self::NAME ] = 'none';

				} elseif( $this->{$device}->enable ) {
					$this->blur = !empty($this->{$device}->blur) ? $this->{$device}->blur : $this->blur;
					$this->brightness = !empty($this->{$device}->brightness) ? $this->{$device}->brightness : $this->brightness;
					$this->opacity = !empty($this->{$device}->opacity) ? $this->{$device}->opacity : $this->opacity;

					$css[$device][self::NAME] = "blur(" . $this->blur . "px) brightness(" . $this->brightness . "%) opacity(" . $this->opacity . "%)";
				}
			}
		}

		return $css;
	}
}
