<?php
namespace TYPO3\CMS\Frontend\Page;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Interface for classes which hook into pageSelect and do additional getPage processing
 *
 * @author Christian Kuhn <lolli@schwarzbu.ch>
 */
interface PageRepositoryGetPageHookInterface {

	/**
	 * Modifies the DB params
	 *
	 * @param int $uid The page ID
	 * @param bool $disableGroupAccessCheck If set, the check for group access is disabled. VERY rarely used
	 * @param \TYPO3\CMS\Frontend\Page\PageRepository $parentObject Parent object
	 * @return void
	 */
	public function getPage_preProcess(&$uid, &$disableGroupAccessCheck, \TYPO3\CMS\Frontend\Page\PageRepository $parentObject);

}
