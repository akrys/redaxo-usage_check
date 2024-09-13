<?php

/**
 * Datei für ...
 *
 * @author        akrys
 */
namespace FriendsOfRedaxo\UsageCheck\Exception;

/**
 * Description of MediumNotFoundException
 *
 * @author akrys
 */
class MediaNotFoundException extends \Exception
{

	public function __construct(
		private string $filename,
		string $message = "",
		int $code = 0,
		?\Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}

	public function getFilename(): string
	{
		return $this->filename;
	}
}
