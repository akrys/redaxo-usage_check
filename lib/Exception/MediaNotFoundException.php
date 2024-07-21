<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace FriendsOfRedaxo\UsageCheck\Exception;

/**
 * Datei für ...
 *
 * @version       1.0 / 2024-03-24
 * @author        akrys
 */

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
