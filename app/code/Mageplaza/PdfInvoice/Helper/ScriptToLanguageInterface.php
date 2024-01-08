<?php

namespace Mageplaza\PdfInvoice\Helper;

interface ScriptToLanguageInterface
{

	public function getLanguageByScript($script);

	public function getLanguageDelimiters($language);

}
