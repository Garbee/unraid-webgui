<?PHP
/* Copyright 2005-2023, Lime Technology
 * Copyright 2014-2023, Guilherme Jardim, Eric Schultz, Jon Panozzo.
 * Copyright 2012-2023, Bergware International.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
?>
<?
$docroot = $docroot ?? $_SERVER['DOCUMENT_ROOT'] ?: '/usr/local/emhttp';

// add translations
$_SERVER['REQUEST_URI'] = 'docker';
require_once "$docroot/webGui/include/Translations.php";
require_once "$docroot/webGui/include/Secure.php";
require_once "$docroot/webGui/include/Wrappers.php";
require_once "$docroot/plugins/dynamix.docker.manager/include/DockerClient.php";

$DockerClient = new DockerClient();
$action       = unscript(_var($_REQUEST,'action'));
$container    = unbundle(_var($_REQUEST,'container'));
$name         = unscript(_var($_REQUEST,'name'));
$image        = unscript(_var($_REQUEST,'image'));
$arrResponse  = ['error' => _('Missing parameters')];

switch ($action) {
case 'start':
	if ($container) $arrResponse = ['success' => $DockerClient->startContainer($container)];
	break;
case 'pause':
	if ($container) $arrResponse = ['success' => $DockerClient->pauseContainer($container)];
	break;
case 'stop':
	if ($container) $arrResponse = ['success' => $DockerClient->stopContainer($container)];
	break;
case 'resume':
	if ($container) $arrResponse = ['success' => $DockerClient->resumeContainer($container)];
	break;
case 'restart':
	if ($container) $arrResponse = ['success' => $DockerClient->restartContainer($container)];
	break;
case 'remove_container':
	if ($container) $arrResponse = ['success' => $DockerClient->removeContainer($name, $container, 1)];
	break;
case 'remove_image':
	if ($image) $arrResponse = ['success' => $DockerClient->removeImage($image)];
	break;
case 'remove_all':
	if ($container && $image) {
		// first: try to remove container
		$ret = $DockerClient->removeContainer($name, $container, 2);
		if ($ret === true) {
			// next: try to remove image
			$arrResponse = ['success' => $DockerClient->removeImage($image)];
		} else {
			// error: container failed to remove
			$arrResponse = ['success' => $ret];
		}
	}
	break;
default:
	$arrResponse = ['error' => _('Unknown action')." '$action'"];
	break;
}

header('Content-Type: application/json');
die(json_encode($arrResponse));
