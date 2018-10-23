<?php

/**
 * Datei für ...
 *
 * @version       1.0 / 2018-05-12
 * @author        akrys
 */
namespace akrys\redaxo\addon\UsageCheck\Lib;

/**
 * Description of FileSize
 *
 * @author akrys
 */
class FileSize
{
	/**
	 * Dateigröße, die analysiert werden soll.
	 * @var int
	 */
	private $size;

	/**
	 * Konstruktor
	 *
	 * @param int $size
	 */
	public function __construct($size)
	{
		$this->size = $size;
	}

	/**
	 * Dateigröße ermitteln.
	 *
	 * Die Größe in Byte auszugeben ist nicht gerade übersichtlich. Daher wird
	 * hier versucht den Wert in der größt-möglichen Einheit zu ermittln.
	 *
	 * @return string
	 */
	public function getSizeOut()
	{
		$value = $this->getSizeReadable($this->size);

		$value['size'] = round($value['size'], 2);
		switch ($value['index']) {
			case 0:
				$unit = 'B';
				break;
			case 1:
				$unit = 'kB';
				break;
			case 2:
				$unit = 'MB';
				break;
			case 3:
				$unit = 'GB';
				break;
			case 4:
				$unit = 'TB';
				break;
			case 5:
				$unit = 'EB';
				break;
			case 6:
				$unit = 'PB';
				break;
			default:
				$unit = '????';
				break;
		}

		return $value['size'].' '.$unit;
	}

	/**
	 * kleinste Speichereinheit ermittln.
	 *
	 * Dabei zählen, wie oft man sie verkleinern konnte. Daraus ergibt sich die Einheit.
	 *
	 * @param int $size
	 * @return array Indezes: index, size
	 */
	private function getSizeReadable($size)
	{
		$return = array(
			'index' => 0,
			'size' => $size,
		);

		$return['index'] = 0;

		while ($return['size'] > 1024 && $return['index'] <= 6) {
			$return['index'] ++;
			$return['size'] /= 1024;
		}
		return $return;
	}
}
