<?php

namespace Mageplaza\PdfInvoice\Helper;

class NumericString
{

	public static function containsPercentChar($string)
	{
		return stristr($string, '%');
	}

	public static function removePercentChar($string)
	{
		return str_replace('%', '', $string);
	}

}
