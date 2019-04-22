<?php
/**
 * Copy Right Anhuike.com
 * Each engineer has a duty to keep the code elegant
 * Author shzhrui<anhuike@gmail.com>
 * $Id: modifier.amount.php 9343 2015-03-24 07:07:00Z youyi $
 */

function smarty_modifier_amount($amount, $precision=2, $prefix='￥')
{
	$precision = (int)$precision;
	return $prefix.round($amount, $precision);
}