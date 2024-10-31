<?php
namespace App\Security;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlSanitizer
{
	private $purifier;

	public function __construct()
	{
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Allowed', 'p,h2,h3,h4,strong,em,ul,ol,li,a[href],br');
		$config->set('Attr.AllowedFrameTargets', ['_blank']);
		$this->purifier = new HTMLPurifier($config);
	}

	public function sanitize(string $dirtyHtml): string
	{
		return $this->purifier->purify($dirtyHtml);
	}
}
