<?php
namespace Depicter\Document\Models\Elements;

use Averta\Core\Utility\Data;
use Averta\WordPress\Utility\JSON;
use Depicter;
use Depicter\Document\CSS\Breakpoints;
use Depicter\Document\Models;
use Depicter\Front\Preview;
use Depicter\Html\Html;
use Depicter\Media\Image\FileResizedFinder;
use Depicter\Services\MediaBridge;

class Image extends Models\Element
{

	/**
	 * @var array
	 */
	protected $renderArgs = [];

	/**
	 * Whether in preview mode or not
	 *
	 * @var bool
	 */
	protected $isPreviewMode;

	/**
	 * List of breakpoint sizes
	 *
	 * @var array
	 */
	protected $breakpoints;

	/**
	 * Stores inherited crop data options
	 *
	 * @var array
	 */
	protected $inheritedOptions = [];


	/**
	 * Calculates render options
	 *
	 * @return $this
	 */
	protected function setRenderOptions(){
		$this->breakpoints = Breakpoints::all();
		$this->breakpoints['default'] = 1025;

		$this->inheritedOptions = [
			'resizeW' => null,
			'resizeH' => null,
			'cropW'   => null,
			'cropH'   => null,
			'focalX'  => 0.5,
			'focalY'  => 0.5,
			'cropData'=> null
		];

		$this->renderArgs['isPreview'] = Depicter::front()->preview()->isPreview();
		$this->renderArgs['isSVG'] = $this->isSvg( $this->options->source );
		$this->renderArgs['sizeUnit'] = isset( $this->size->default->width->unit ) ? $this->size->default->width->unit : 'px';
		$this->renderArgs['assetId'] = $this->options->source;
		$this->renderArgs['attachmentId'] = Depicter::media()->getAttachmentId( $this->options->source );
		$this->renderArgs['isAttachment'] = is_numeric( $this->renderArgs['attachmentId'] );
		$this->renderArgs['altText'] = Depicter::media()->getAltText( $this->renderArgs['attachmentId'] );

		$this->isPreviewMode = $this->renderArgs['isPreview'] || $this->renderArgs['isSVG'] || ! $this->renderArgs['isAttachment'];

		return $this;
	}

	/**
	 * Whether it's preview mode or not
	 *
	 * @return bool
	 */
	protected function isPreviewMode(){
		return $this->isPreviewMode;
	}


	protected function getUnitSize( $device = 'default' ){
		 return isset( $this->size->{$device}->width->unit ) ? $this->size->{$device}->width->unit : 'px';
	}

	protected function hasRelativeUnitSize( $device = 'default' ){
		$relativeUnits = ['%'];

		if( isset( $this->size->{$device}->width->unit ) && in_array( $this->size->{$device}->width->unit, $relativeUnits )  ){
			return true;
		}
		if( isset( $this->size->{$device}->height->unit ) && in_array( $this->size->{$device}->height->unit, $relativeUnits )  ){
			return true;
		}
		return false;
	}

	/**
	 * Render the element wrapper tag
	 *
	 * @throws \JsonMapper_Exception
	 */
	protected function renderPictureWrapper(){

		$args = $this->getDefaultAttributes();

		$devices = Breakpoints::names();
		foreach( $devices as $device ) {
			$dataAttrName = $device == 'default' ? 'data-crop' : 'data-' . $device . '-crop';
			$hasRelativeUnitSize = $this->hasRelativeUnitSize( $device );

			if ( $this->isPreviewMode || $hasRelativeUnitSize ) {

				if( ! empty( $this->cropData->{$device} ) ){
					$args[ $dataAttrName ] = [
						'mediaSize'=> [
							'width'  => $this->cropData->{$device}->mediaSize->width,
							'height' => $this->cropData->{$device}->mediaSize->height
						]
					];
					$args[ $dataAttrName ]['focalPoint'] = [
						'x' => ! empty( $this->cropData->{$device}->focalPoint->x ) ? $this->cropData->{$device}->focalPoint->x : 0.5,
						'y' => ! empty( $this->cropData->{$device}->focalPoint->y ) ? $this->cropData->{$device}->focalPoint->y : 0.5
					];

					$args[ $dataAttrName ] = JSON::encode( $args[ $dataAttrName ] );
				}

			} elseif( ! $hasRelativeUnitSize && ! empty( $this->cropData->{$device} ) && $this->inheritedOptions['cropData'] !== "false" ){
				$args[ $dataAttrName ] = "false";
			}

			if( isset( $args[ $dataAttrName ] ) ){
				$this->inheritedOptions['cropData'] = $args[ $dataAttrName ];
			}
		}

		$this->markup = Html::picture( $args );
	}

	/**
	 * Renders a default image tag
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	protected function renderImageTag(){
		list( $resizeWidth, $resizeHeight, $cropWidth, $cropHeight, $args ) = $this->getImageEditOptions( 'default' );

		$hasRelativeUnitSize = $this->hasRelativeUnitSize( 'default' );

		if ( $this->isPreviewMode ) {
			$imageSource = Depicter::media()->getSourceUrl( $this->renderArgs['assetId'], [ $cropWidth, $cropHeight ], false, [ 'dry' => true ] );

		} else {
			$args['upscale'] = true;

			if( $hasRelativeUnitSize ){
				$cropWidth = null;
				$cropHeight = null;
			}
			$imageSource = Depicter::media()->resizeSourceUrl(
				$this->renderArgs['attachmentId'], $resizeWidth, $resizeHeight, $cropWidth, $cropHeight, $args
			);
		}

		$img = Html::img( '', [
			'src' => \Depicter::media()::IMAGE_PLACEHOLDER_SRC,
			'data-src' => $imageSource,
			'alt' => $this->renderArgs['altText']
		] );

		if ( $this->isPreviewMode && ! $hasRelativeUnitSize ) {
			$img = Html::source([
				'data-srcset' => $imageSource,
			]) . $img;
		}

		if ( false !== $a = $this->getLinkTag() ) {
			return $this->markup->nest( $a->nest( "\n" .$img . "\n\t\t" ) . "\n" );
		}

		$this->markup->nest( "\n" .$img . "\n\t\t" );
	}

	/**
	 * Renders element markup
	 *
	 * @return string|void
	 * @throws \JsonMapper_Exception
	 * @throws \Exception
	 */
	public function render() {

		if ( empty( $this->options->source ) ) {
			return '';
		}

		$this->setRenderOptions();
		$this->renderPictureWrapper();

		$this->renderSourceTags();
		$this->renderImageTag();

		return $this->markup;
	}


	/**
	 * Retrieves attachment mime type
	 *
	 * @param string|int $assetId
	 *
	 * @return false|string
	 */
	protected function getMimeType( $assetId ){
		$attachmentId = Depicter::media()->getAttachmentId( $assetId );
		return is_numeric( $attachmentId ) ? get_post_mime_type( $attachmentId ) : false;
	}

	/**
	 * Whether it's svg file or not
	 *
	 * @param string|int $assetId
	 *
	 * @return bool
	 */
	public function isSvg( $assetId ) {
		return $this->getMimeType( $assetId ) == 'image/svg+xml';
	}

	/**
	 * Retrieves image edit options for a breakpoint
	 *
	 * @param string $device
	 *
	 * @return array
	 */
	protected function getImageEditOptions( $device = 'default' ){
		$params = [];

		$params[] = $this->inheritedOptions['resizeW'] = !empty( $this->cropData->{$device}->mediaSize->width  ) ?
											$this->cropData->{$device}->mediaSize->width :
											null;

		$params[] = $this->inheritedOptions['resizeH'] = !empty( $this->cropData->{$device}->mediaSize->height ) ?
											$this->cropData->{$device}->mediaSize->height :
											null;

		$params[] = $this->inheritedOptions['cropW'] = !empty( $this->size->{$device}->width->value  ) ?
											$this->size->{$device}->width->value :
											null;

		$params[] = $this->inheritedOptions['cropH'] = !empty( $this->size->{$device}->height->value ) ?
											$this->size->{$device}->height->value :
											null;

		$this->inheritedOptions['focalX'] = !empty( $this->cropData->{$device}->focalPoint->x ) ?
											$this->cropData->{$device}->focalPoint->x :
											$this->inheritedOptions['focalX'];

		$this->inheritedOptions['focalY'] = !empty( $this->cropData->{$device}->focalPoint->y ) ?
											$this->cropData->{$device}->focalPoint->y :
											$this->inheritedOptions['focalY'];

		$params[] = [
			'focalX' => $this->inheritedOptions['focalX'],
			'focalY' => $this->inheritedOptions['focalY']
		];

		return $params;
	}

	/**
	 * Retrieves source urls (srcset) for a breakpoint in array
	 *
	 * @param string $device
	 *
	 * @return array
	 */
	protected function getSourceUrls( $device = 'default' ){

		list( $resizeWidth, $resizeHeight, $cropWidth, $cropHeight, $args ) = $this->getImageEditOptions( $device );

		if( ! $cropWidth && ! $cropHeight && ! $resizeWidth && ! $resizeHeight ){
			return [];
		}

		$args['upscale'] = true;

		if( $this->hasRelativeUnitSize( $device ) ){
			$cropWidth = null;
			$cropHeight = null;
		}

		$imageSources = [];
		$imageSources[] = Depicter::media()->resizeSourceUrl(
			$this->renderArgs['attachmentId'], $resizeWidth, $resizeHeight, $cropWidth, $cropHeight, $args
		);

		if( empty( $imageSources ) ){
			return [];
		}

		// $args['upscale'] = false;

		$retinaImageSource = Depicter::media()->resizeSourceUrl(
			$this->renderArgs['attachmentId'],
			$resizeWidth  ? $resizeWidth  * 2 : $resizeWidth,
			$resizeHeight ? $resizeHeight * 2 : $resizeHeight,
			 $cropWidth  ? $cropWidth  * 2 : $cropWidth,
			 $cropHeight ? $cropHeight * 2 : $cropHeight,
			$args
		);

		if( $retinaImageSource ){
			$imageSources[] = $retinaImageSource . ' 2x';
		}

		return $imageSources;
	}

	/**
	 * Generates and appends a source tag with media query to element markup
	 *
	 * @param array  $imageSources
	 * @param string $mediaQueryCondition
	 * @param int   $mediaQuerySize
	 */
	protected function appendSourceTag( $imageSources = [], $mediaQueryCondition = 'max-width', $mediaQuerySize = null ){
		if( ! $imageSources ){
			return;
		}
		$attributes = [
			'data-srcset' => trim( implode( ', ', $imageSources ), ', ' )
		];
		if( $mediaQueryCondition && $mediaQuerySize ){
			$attributes['media'] = '(' . $mediaQueryCondition . ': ' . $mediaQuerySize . 'px)';
		}
		$sourceTag = Html::source( $attributes );

		$this->markup->nest( "\n" . $sourceTag . "\n\t\t" );
	}

	/**
	 * Renders and appends necessary source tags with media queries for breakpoints
	 */
	protected function renderSourceTags(){
		if( $this->isPreviewMode ){
			return;
		}

		$desktopSources = $this->getSourceUrls( 'default' );
		$tabletSources  = $this->getSourceUrls( 'tablet' );
		$mobileSources  = $this->getSourceUrls( 'mobile' );

		if( ! $tabletSources && ! $mobileSources  ){
			$this->appendSourceTag( $desktopSources );
			return;
		}

		if( $desktopSources == $tabletSources ){
			if( $tabletSources == $mobileSources ){
				// if all breakpoints sources are the same
				$this->appendSourceTag( $desktopSources );
			} else {
				// if desktop and tablet sources are the same
				$this->appendSourceTag( $mobileSources, 'max-width', $this->breakpoints['mobile'] );
				$this->appendSourceTag( $desktopSources, 'min-width', $mobileSources ? (int) $this->breakpoints['mobile'] + 1 : 0 );
			}
		} elseif( $tabletSources == $mobileSources ){
			// if tablet and mobile sources are the same
			$this->appendSourceTag( $tabletSources, 'max-width', $this->breakpoints['tablet'] );
			$this->appendSourceTag( $desktopSources, 'min-width', ( $tabletSources ? (int) $this->breakpoints['tablet'] + 1 : 0 ) );
		} else {
			$this->appendSourceTag( $mobileSources, 'max-width', $this->breakpoints['mobile'] );
			$this->appendSourceTag( $tabletSources, 'max-width', $this->breakpoints['tablet'] );

			$mediaQuerySize = (int) $this->breakpoints['default'];
			if( ! $tabletSources ){
				$mediaQuerySize = $mobileSources ? (int) $this->breakpoints['mobile'] + 1 : 0;
			}
			$this->appendSourceTag( $desktopSources, 'min-width', $mediaQuerySize );
		}

	}

}
