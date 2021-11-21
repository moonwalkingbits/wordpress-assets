<?php
/**
 * Assets: Target location enum
 *
 * @package Moonwalking_Bits\Assets
 * @author Martin Pettersson
 * @license GPL-2.0
 * @since 0.1.0
 */

namespace Moonwalking_Bits\Assets;

use Moonwalking_Bits\Enum\Abstract_Enum;

/**
 * Represents a fixed set of script target locations.
 *
 * @since 0.1.0
 * @see \Moonwalking_Bits\Enum\Abstract_Enum
 */
final class Target_Location extends Abstract_Enum {

	const HEADER = 'HEADER';
	const FOOTER = 'FOOTER';
}
