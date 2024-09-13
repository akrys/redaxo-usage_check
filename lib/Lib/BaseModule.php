<?php

/**
 * Datei für ...
 *
 * @author        akrys
 */
namespace FriendsOfRedaxo\UsageCheck\Lib;

/**
 * Description of ModulesBase
 *
 * @author akrys
 */
abstract class BaseModule extends RexBase
{
	/**
	 * Anzeigemodus
	 * @var boolean
	 */
	protected bool $showAll = false;

	/**
	 * Tabellenfelder
	 * @var array<string, mixed>
	 */
	protected array $tableFields = [];

	/**
	 * Anzeigemodus umstellen
	 * @param boolean $bln
	 */
	public function showAll(bool $bln): void
	{
		$this->showAll = $bln;
	}

	/**
	 * Rechte prüfen
	 * @return bool
	 */
	abstract public function hasPerm(): bool;

	/**
	 * Daten holen
	 * @return array<int|string, mixed>
	 */
	abstract public function get(): array;

	/**
	 * SQL genereieren
	 * @param int $datail_id
	 * @return string
	 */
	abstract protected function getSQL(int $datail_id = null): string;

	/**
	 * Details holen
	 * @param int $item_id
	 * @return array<string, mixed>
	 */
	abstract public function getDetails(int $item_id): array;
}
