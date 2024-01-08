<?php

namespace Mageplaza\PdfInvoice\Helper;

interface BarcodeInterface
{

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return mixed[]
	 */
	public function getData();

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getKey($key);

	/**
	 * @return string
	 */
	public function getChecksum();

}
