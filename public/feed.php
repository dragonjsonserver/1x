<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled with this
 * package in the file LICENSE.txt. It is also available through the
 * world-wide-web at this URL: http://dragonjsonserver.de/license. If you did
 * not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@dragonjsonserver.de. So we
 * can send you a copy immediately.
 *
 * @copyright Copyright (c) 2012 DragonProjects (http://dragonprojects.de)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @author Christoph Herrmann <developer@dragonjsonserver.de>
 */

require 'bootstrap.php';
$format = 'rss';
if (isset($_GET['format']) && $_GET['format'] == 'atom') {
	$format = $_GET['format'];
}
$feed = new Zend_Feed_Writer_Feed();
$application = new Dragon_Application_Config('dragon/application/application');
$feed->setTitle($application->name);
$feed->setDescription($application->name . ' ' . $application->version . ' ' . $application->copyright);
$feed->setLink(BASEURL);
$feed->setFeedLink(BASEURL . 'feed.php?format=' . $format, $format);
$imprint = new Dragon_Application_Config('dragonx/homepage/imprint');
$feed->addAuthor(array(
    'name'  => $imprint->webmaster,
    'email' => $imprint->mailingaddress,
    'uri'   => BASEURL,
));
$news = new Dragon_Application_Config('dragonx/homepage/news');
if (count($news->news) > 0) {
	$feed->setDateModified($news->news->{0}->timestamp);
	foreach ($news->news as $news) {
		$entry = $feed->createEntry();
		$entry->setTitle($news->title);
		$entry->setLink(BASEURL);
		$entry->addAuthor(array(
		    'name'  => $imprint->webmaster,
		    'email' => $imprint->mailingaddress,
		    'uri'   => BASEURL,
		));
		$entry->setDateModified($news->timestamp);
		$entry->setDateCreated($news->timestamp);
        $entry->setDescription($news->content);
		$feed->addEntry($entry);
	}
} else {
    $feed->setDateModified(0);
}
echo $feed->export($format);
