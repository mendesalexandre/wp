<?php
namespace Depicter\Document\Models\Options;

class General
{
	/**
	 * @var int
	 */
	public $fullscreenMargin;

	/**
	 * @var bool
	 */
	public $autoHeight;

	/**
	 * @var bool
	 */
	public $keepAspect;

	/**
	 * @var Unit
	 */
	public $minHeight;

	/**
	 * @var Unit
	 */
	public $maxHeight;

	/**
	 * @var States
	 */
	public $visible;

	/**
	 * @var string
	 */
	public $backgroundColor = '';

	/**
	 * @var All
	 */
	protected $allOptions;


	public function setAllOptions( $allOptions ){
		$this->allOptions = $allOptions;
	}

	public function getAllOptions(){
		return $this->allOptions;
	}

	public function getStylesList(){
		$styles = [
			'default' => []
		];

		// ignore min and max height if autoHeight is enabled
		if( $this->getAllOptions()->getLayout() !== 'fullscreen' && ! $this->autoHeight ) {
			if( ! empty( $this->minHeight ) ){
				$styles['default']['min-height'] = $this->minHeight;
			}
			if( ! empty( $this->maxHeight ) ){
				$styles['default']['max-height'] = $this->maxHeight;
			}
		}

		if( $this->backgroundColor ){
			$styles['default']['background-color'] = $this->backgroundColor;
		}

		return $styles;
	}


	/**
	 * Get before init document styles
	 *
	 * @return array
	 */
	public function getBeforeInitStyles(){
		$styles = [
			'default' => []
		];
		$layout = $this->getAllOptions()->getLayout();

		if( $layout == 'fullscreen' ){
			if( $this->fullscreenMargin ){
				$styles['default']['height'] = "calc( 100vh - {$this->fullscreenMargin}px )";
			}
		} elseif( $layout == 'boxed' ){
			$responsiveSizes = $this->getAllOptions()->getSizes('width', true);
			foreach ( $responsiveSizes as $device => $value ){
				$styles[ $device ][ 'width' ] = $value;
			}

			$responsiveSizes = $this->getAllOptions()->getSizes('height', true);
			foreach ( $responsiveSizes as $device => $value ){
				$styles[ $device ][ 'height' ] = $value;
			}
		} elseif( $layout == 'fullwidth' ){
			$responsiveSizes = $this->getAllOptions()->getSizes('height', true);
			foreach ( $responsiveSizes as $device => $value ){
				$styles[ $device ][ 'height' ] = $value;
			}
		}

		return $styles;
	}


}
