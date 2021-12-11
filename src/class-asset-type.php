<?php
/**
 * Assets: Asset type enum
 *
 * @package Moonwalking_Bits\Assets
 * @author Martin Pettersson
 * @license GPL-2.0
 * @since 0.1.0
 */

namespace Moonwalking_Bits\Assets;

use Moonwalking_Bits\Enum\Abstract_Enum;

/**
 * Represents a fixed set of asset types.
 *
 * @since 0.1.0
 * @see \Moonwalking_Bits\Enum\Abstract_Enum
 */
class Asset_Type extends Abstract_Enum {

	const STYLE  = 'style';
	const SCRIPT = 'script';
}
