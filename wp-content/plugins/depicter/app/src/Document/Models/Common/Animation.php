<?php
namespace Depicter\Document\Models\Common;


use Averta\WordPress\Utility\JSON;
use Depicter\Document\CSS\Breakpoints;

class Animation
{
	/**
	 * Animation in phase
	 *
	 * @var object
	 */
	public $in;

	/**
	 * Animation out phase
	 *
	 * @var object
	 */
	public $out;

	/**
	 * @var bool|null
	 */
	public $waitForAction;

	/**
	 * Known phases
	 *
	 * @var array
	 */
	protected $phases = ['in', 'out'];


	/**
	 * @param string $phase
	 * @param string $breakpoint
	 *
	 * @return false|string
	 */
	public function getAnimation( $phase, $breakpoint ) {

		if( empty( $this->{$phase}->data->{$breakpoint}->type ) ){
			return false;
		}

		$params = [
			'type' => isset( $this->{$phase}->data->{$breakpoint}->type ) ? $this->{$phase}->data->{$breakpoint}->type : '',
		];

		if( isset( $this->{$phase}->data->{$breakpoint}->params->delay ) ){
			$params[ 'delay' ] = $this->{$phase}->data->{$breakpoint}->params->delay;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->duration ) ){
			$params[ 'duration' ] = $this->{$phase}->data->{$breakpoint}->params->duration;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->easing ) ){
			$params[ 'easing' ] = $this->{$phase}->data->{$breakpoint}->params->easing;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->direction ) ){
			$params[ 'direction' ] = $this->{$phase}->data->{$breakpoint}->params->direction;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->movement ) ){
			$params[ 'movement' ] = $this->{$phase}->data->{$breakpoint}->params->movement;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->x ) ){
			$params[ 'x' ] = $this->{$phase}->data->{$breakpoint}->params->x;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->y ) ){
			$params[ 'y' ] = $this->{$phase}->data->{$breakpoint}->params->y;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->z ) ){
			$params[ 'z' ] = $this->{$phase}->data->{$breakpoint}->params->z;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->perspective ) ){
			$params[ 'perspective' ] = $this->{$phase}->data->{$breakpoint}->params->perspective;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->fade ) ){
			$params[ 'fade' ] = $this->{$phase}->data->{$breakpoint}->params->fade;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->rotate ) ){
			$params[ 'rotate' ] = $this->{$phase}->data->{$breakpoint}->params->rotate;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->rotate3d->x ) ){
			$params[ 'rotateX' ] = $this->{$phase}->data->{$breakpoint}->params->rotate3d->x;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->rotate3d->y ) ){
			$params[ 'rotateY' ] = $this->{$phase}->data->{$breakpoint}->params->rotate3d->y;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->skew->x ) ){
			$params[ 'skewX' ] = $this->{$phase}->data->{$breakpoint}->params->skew->x;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->skew->y ) ){
			$params[ 'skewY' ] = $this->{$phase}->data->{$breakpoint}->params->skew->y;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->scale ) ){
			$params[ 'scale' ] = $this->{$phase}->data->{$breakpoint}->params->scale;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->transformOrigin->x ) && isset( $this->{$phase}->data->{$breakpoint}->params->transformOrigin->y ) ){
			$params[ 'transformOrigin' ] = $this->{$phase}->data->{$breakpoint}->params->transformOrigin->x . ' ' . $this->{$phase}->data->{$breakpoint}->params->transformOrigin->y;
		}
		if( isset( $this->{$phase}->data->{$breakpoint}->params->transformOrigin->z ) ){
			$params[ 'transformOriginZ' ] = $this->{$phase}->data->{$breakpoint}->params->transformOrigin->z;
		}

		return JSON::encode( $params );
	}

	private function getWaitForAnimationAttr( $phase, $breakpoint ) {

	}

	/**
	 * Get all animation attributes
	 *
	 * @return array
	 */
	public function getAnimationAttrs() {
		$attrs = [];

		// Collect animation attributes
		foreach ( Breakpoints::names() as $breakpoint  ){
			foreach ( $this->phases as $phase  ){
				$breakpoint_prefix = $breakpoint ? $breakpoint . '-' : $breakpoint;
				$breakpoint_prefix = $breakpoint == 'default' ? '' : $breakpoint_prefix;
				if( $animation_value = $this->getAnimation( $phase, $breakpoint ) ){
					$attrs[ 'data-'.  $breakpoint_prefix .'animation-' . $phase ] = $this->getAnimation( $phase, $breakpoint );
				}
			}
		}

		// Get animation interactive attributes
		if( ! is_null( $this->waitForAction ) ){
			$attrs[ "data-wait-for-action" ] = $this->waitForAction ? "true" : "false";
		}

		if( isset( $this->out->wait ) ){
			$attrs[ "data-animation-out-wait" ] = $this->out->wait ? "true" : "false";
		}

		// Get animation interactive attributes
		foreach ( $this->phases as $phase  ){
			if( isset( $this->{$phase}->interactive ) ){
				$attrs[ "data-animation-{$phase}-interactive" ] = $this->{$phase}->interactive ? "true" : "false";
			}
		}

		return $attrs;
	}
}
