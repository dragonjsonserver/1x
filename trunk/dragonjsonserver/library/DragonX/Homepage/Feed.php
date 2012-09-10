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

/**
 * Klasse zur Erstellung des RSS und ATOM Feeds aus den Neuigkeiten
 */
class DragonX_Homepage_Feed
{
    /**
     * Erstellt den Feed und gibt diesen zurück
     * @param string $format
     * @return DragonX_Homepage_Feed
     */
    public function getFeed($format)
    {
    	if (!in_array($format, array('rss', 'atom'))) {
    		throw new InvalidArgumentException('invalid format');
    	}
		$feed = new Zend_Feed_Writer_Feed();
		$configApplication = new Dragon_Application_Config('dragon/application/application');
		$feed->setTitle($configApplication->name);
		$feed->setDescription($configApplication->name . ' ' . $configApplication->version . ' ' . $configApplication->copyright);
		$feed->setLink(BASEURL);
		$feed->setFeedLink(BASEURL . 'feed.php?format=' . $format, $format);
		$configImprint = new Dragon_Application_Config('dragonx/homepage/imprint');
		$feed->addAuthor(array(
		    'name'  => $configImprint->webmaster,
		    'email' => $configImprint->mailingaddress,
		    'uri'   => BASEURL,
		));
		$configNews = new Dragon_Application_Config('dragonx/homepage/news');
		if (count($configNews->news) > 0) {
		    $feed->setDateModified($configNews->news->{0}->timestamp);
		    foreach ($configNews->news as $news) {
		        $entry = $feed->createEntry();
		        $entry->setTitle($news->title);
		        $entry->setLink(BASEURL);
		        $entry->addAuthor(array(
		            'name'  => $configImprint->webmaster,
		            'email' => $configImprint->mailingaddress,
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
		return $feed->export($format);
    }
}
