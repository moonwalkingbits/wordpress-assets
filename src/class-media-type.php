<?php
/**
 * Assets: Media type enum
 *
 * @package Moonwalking_Bits\Assets
 * @author Martin Pettersson
 * @license GPL-2.0
 * @since 0.1.0
 */

namespace Moonwalking_Bits\Assets;

use Moonwalking_Bits\Enum\Abstract_Enum;

/**
 * Represents a fixed set of CSS media types.
 *
 * @since 0.1.0
 * @see \Moonwalking_Bits\Enum\Abstract_Enum
 */
final class Media_Type extends Abstract_Enum {

	const SCREEN = 'screen';
	const PRINT  = 'print';
	const SPEECH = 'speech';
	const ALL    = 'all';
}
