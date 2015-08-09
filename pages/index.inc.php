<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */
require $REX['INCLUDE_PATH'].'/layout/top.php';



$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');

include __DIR__.'/_'.$subpage.'.php';


require $REX['INCLUDE_PATH'].'/layout/bottom.php';
