<?php
namespace Depicter\Document\Models\Elements;

use Depicter\Document\Models;
use Depicter\Html\Html;

class Text extends Models\Element
{

	public function render() {
		$tag = isset( $this->options->tag ) ? $this->options->tag : 'p';

		$args = $this->getDefaultAttributes();

		$output =  Html::$tag($args, str_replace("\n", "<br>", $this->options->content) );

		if ( false !== $a = $this->getLinkTag() ) {
			return $a->nest( $output ) . "\n";
		}
		return $output . "\n";
	}
}
