<?php
namespace Depicter\Document\Models;

use Averta\Core\Utility\Arr;
use Depicter\Document\CSS\Selector;
use Depicter\Document\Helper\Helper;
use Depicter\Document\Models\Traits\HasDocumentIdTrait;
use Depicter\Html\Html;

class Section
{
	use HasDocumentIdTrait;

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * List of belonging element ids (assigns with jsonMapper)
	 *
	 * @var array
	 */
	public $elements;

	/**
	 * List of belonging elements
	 *
	 * @var array
	 */
	public $elementObjects;

	/**
	 * @var Common\Size\States
	 */
	public $wrapperSize;

	/**
	 * @var Common\Background
	 */
	public $background;

	/**
	 * @var object
	 */
	public $options;

	/**
	 * @var array|null
	 */
	public $actions;

	/**
	 * @var string
	 */
	public $className = '';

	/**
	 * @var string
	 */
	public $customStyle = '';

	/**
	 * @var array
	 */
	protected $styleList = [];


	/**
	 * get section ID
	 *
	 * @return string
	 */
	public function getID() {
//		return !empty( $this->name ) ? str_replace( ' ', '-', $this->name ) : $this->id;
		return Helper::getSectionIdFromSlug( $this->id );
	}

	/**
	 * get section slug
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->id;
	}

	/**
	 * Get section name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name ? $this->name : $this->id;
	}

	/**
	 * Gets belonging element ids
	 *
	 * @return array
	 */
	public function getElementIds()
	{
		return $this->elements;
	}

	/**
	 * Sets belonging elements
	 *
	 * @param array $elementIds
	 */
	public function setElementIds( $elementIds )
	{
		$this->elements = $elementIds;
	}

	/**
	 * Sets belonging elements
	 *
	 * @param array $elementObjects
	 */
	public function setElementObjects( $elementObjects )
	{
		$this->elementObjects = $elementObjects;
	}

	/**
	 * Render Section
	 */
	public function render() {

		// default Section properties
		$args = [
			'id'          => $this->getCssID(),
			'class'	      => $this->getClassNames(),
			'data-name'   => $this->getName()
		];

		if ( !empty( $this->wrapperSize ) ) {
			$sectionWidth = $this->wrapperSize->getResponsiveSizes( "width"  );
			if ( ! $this->wrapperSize->hasNoResponsiveSize( $sectionWidth ) ) {
				$args[ 'data-wrapper-width'] = implode( ',', $sectionWidth );
			}
			$sectionHeight = $this->wrapperSize->getResponsiveSizes( "height"  );
			if ( ! $this->wrapperSize->hasNoResponsiveSize( $sectionHeight ) ) {
				$args['data-wrapper-height'] = implode( ',', $sectionHeight );
			}
		}

		if ( !empty( $this->options ) ) {
			// check for autoplay option
			if ( !empty( $this->options->slideshowDuration ) ) {
				$args['data-slideshow-duration'] = $this->options->slideshowDuration;
			}
			// check for autoplay option
			if ( !empty( $this->options->pauseSlideshow ) ) {
				$args['data-slideshow-pause'] = $this->options->pauseSlideshow ? "true" : "false";
			}
		}

		$actions = Helper::getActions( $this->actions );
		if ( !empty( $actions ) ) {
			$args = Arr::merge( $args, [
				'data-actions' => $actions
			]);
		}

		$div = Html::div($args);

		// check if section has link
		if ( !empty( $this->options->url->enable ) ) {
			// Section link anchor element should always have target="_black" unless openInNewTab is false.
			$urlArgs = isset( $this->options->url->openInNewTab ) && ! $this->options->url->openInNewTab ? [] : ['target' => '_blank'];
			$urlArgs['class'] = Selector::prefixify('section-link');
			if( ! empty( $this->options->url->path ) ){
				$div->nest( Html::a( '', $this->options->url->path, $urlArgs ) . "\n" );
			}
		}
		if( $sectionBackground = $this->background->render() ){
			$div->nest( "\n" . $this->background->render() . "\n\n" );
		}

		$this->styleList = Arr::merge( $this->styleList, $this->getSelectorAndCssList() );

		if ( !empty( $this->elementObjects ) ) {
			foreach ( $this->elementObjects as $elementObject ) {
				// Get element style
				$this->styleList = Arr::merge( $this->styleList, $elementObject->prepare()->getSelectorAndCssList() );

				$div->nest( $elementObject->prepare()->render() ) ;
			}
		}

		return "\n" . $div;
	}

	/**
	 * Collect element font and add it to document font service
	 */
	public function collectElementFonts(){
		if ( !empty( $this->elementObjects ) ) {
			foreach ( $this->elementObjects as $elementObject ) {
				$elementObject->prepare()->getFontsList();
			}
		}
	}

	/**
	 * Get section class names.
	 *
	 * @return string
	 */
	protected function getClassNames(){
		$classes = [];

		$classes[] = Selector::prefixify(Selector::SECTION_PREFIX );
		$classes[] = $this->getSelector();
		$classes[] = $this->getCustomClassName();

		return trim( implode(' ', $classes) );
	}

	/**
	 * Retrieves custom class name of document
	 *
	 * @return string
	 */
	protected function getCustomClassName(){
		if ( ! empty( $this->className ) ) {
			return $this->className;
		}
		return '';
	}

	/**
	 * Retrieves custom styles of document
	 *
	 * @return string
	 */
	protected function getCustomStyles(){
		if ( ! empty( $this->customStyle ) ) {
			$customStyles =  $this->customStyle;
			// replace "selector" with unique selector of section
			return str_replace('selector', '.'.$this->getStyleSelector(), $customStyles );
		}
		return '';
	}

	/**
	 * Get Elements Style that presented in this section.
	 *
	 * @return array
	 */
	public function getCss() {
		return $this->styleList;
	}

	/**
	 * Get section CSS ID
	 *
	 * @return string
	 */
	public function getCssID() {
		return Helper::getSectionCssId( $this->getDocumentID(), $this->getID() );
	}

	/**
	 * Get section selector
	 *
	 * @return string
	 */
	public function getSelector() {
		return Selector::getUniqueSelector( $this->getDocumentID(), $this->getID(),  null, Selector::SECTION_PREFIX );
	}

	/**
	 * Get section style selector
	 *
	 * @return string
	 */
	public function getStyleSelector() {
		return Selector::PREFIX_CSS . " ." . $this->getSelector();
	}

	/**
	 * Get list of selector and CSS for section
	 *
	 * @return array
	 */
	protected function getSelectorAndCssList(){
		$styleList = [];

		$styleList[ '.' . $this->getStyleSelector() ] = $this->background->getColor();
		$styleList[ '.' . $this->getStyleSelector() . ' .' . $this->background->getContainerClassName() . '::after' ] = $this->background->getOverlayStyles();
		$styleList[ '.' . $this->getStyleSelector() . ' .' . $this->background->getContainerClassName() ] = $this->background->getSectionBackgroundFilter();

		if ( $this->getCustomStyles() ) {
			$styleList[ '.' . $this->getStyleSelector() ]['customStyle'] = $this->getCustomStyles();
		}

		return $styleList;
	}
}
