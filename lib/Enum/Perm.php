<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPEnum.php to edit this template
 */
namespace FriendsOfRedaxo\UsageCheck\Enum;

/**
 *
 * @author akrys
 */
enum Perm: string
{
	case PERM_TEMPLATE = 'template';
	case PERM_MEDIAPOOL = 'mediapool';
	case PERM_MEDIA = 'media';
	case PERM_MODUL = 'modules';
	case PERM_STRUCTURE = 'structure';
}
