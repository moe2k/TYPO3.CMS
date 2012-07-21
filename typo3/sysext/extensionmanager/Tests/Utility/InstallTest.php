<?php
	/***************************************************************
	 * Copyright notice
	 *
	 * (c) 2012
	 * All rights reserved
	 *
	 * This script is part of the TYPO3 project. The TYPO3 project is
	 * free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html.
	 *
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/

	/**
	 * Testcase for the Tx_Extensionmanager_Utility_List class in the TYPO3 Core.
	 *
	 * @package TYPO3
	 * @subpackage extensionmanager
	 */
class Tx_Extensionmanager_Utility_InstallTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	public $extListGlobal;
	public $loadedExtGlobal;
	public $extension;
	public $fakedExtensions;
	public $installMock;
	protected $listUtilityMock;

	public function setUp() {
		$this->extension = 'dummy';
		$this->listUtilityMock = $this->getAccessibleMock('Tx_Extensionmanager_Utility_List', array());
		$this->installMock = $this->getAccessibleMock(
			'Tx_Extensionmanager_Utility_Install',
			array('writeNewExtensionList', 'processDatabaseUpdates', 'reloadCaches', 'saveDefaultConfiguration')
		);
	}

	public function tearDown() {
		foreach ($this->fakedExtensions as $extension => $_dummy) {
			t3lib_div::rmdir(PATH_site . 'typo3temp/' . $extension, TRUE);
		}
	}

	/**
	 * Creates a fake extension inside typo3temp/. No configuration is created,
	 * just the folder
	 *
	 * @return string The extension key
	 */
	protected function createFakeExtension() {
		$extKey = strtolower(uniqid('testing'));
		$absExtPath = PATH_site . "typo3temp/$extKey/";
		$relPath = "typo3temp/$extKey/";
		t3lib_div::mkdir($absExtPath);

		$this->fakedExtensions[$extKey] = array(
			'siteRelPath' => $relPath
		);

		return $extKey;
	}

	/**
	 * @test
	 */
	public function installCallsProcessDatabaseUpdates() {
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableAndInstalledExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('enrichExtensionsWithEmConfInformation')
			->will($this->returnValue(array($this->extension => array('key' => $this->extension))));
		$this->installMock->_set('listUtility', $this->listUtilityMock);
		$this->installMock->expects($this->once())->method('processDatabaseUpdates')->with(array('key' => $this->extension));
		$this->installMock->install($this->extension);
	}
	/**
	 * @test
	 */
	public function installCallsWriteNewExtensionList() {
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableAndInstalledExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('enrichExtensionsWithEmConfInformation')
			->will($this->returnValue(array($this->extension => array('key' => $this->extension))));
		$this->installMock->_set('listUtility', $this->listUtilityMock);
		$this->installMock->expects($this->once())->method('writeNewExtensionList');
		$this->installMock->install($this->extension);
	}
	/**
	 * @test
	 */
	public function installCallsFlushCachesIfClearCacheOnLoadIsSet() {
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableAndInstalledExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('enrichExtensionsWithEmConfInformation')
			->will($this->returnValue(array($this->extension => array('key' => $this->extension, 'clearcacheonload' => '1'))));

		$this->installMock->_set('listUtility', $this->listUtilityMock);
		$backupCacheManager = $GLOBALS['typo3CacheManager'];
		$GLOBALS['typo3CacheManager'] = $this->getMock('t3lib_cache_manager');
		$GLOBALS['typo3CacheManager']->expects($this->once())->method('flushCaches');
		$this->installMock->install($this->extension);
		$GLOBALS['typo3CacheManager'] = $backupCacheManager;
	}

	/**
	 * @test
	 */
	public function installCallsReloadCaches() {
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableAndInstalledExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('enrichExtensionsWithEmConfInformation')
			->will($this->returnValue(array($this->extension => array('key' => $this->extension, 'clearcacheonload' => '1'))));

		$this->installMock->_set('listUtility', $this->listUtilityMock);
		$this->installMock->expects($this->once())->method('reloadCaches');
		$this->installMock->install('dummy');
	}

	/**
	 * @test
	 */
	public function installCallsSaveDefaultConfigurationWithExtensionKey() {
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('getAvailableAndInstalledExtensions')
			->will($this->returnValue(array($this->extension => $this->extension)));
		$this->listUtilityMock
			->expects($this->once())->method('enrichExtensionsWithEmConfInformation')
			->will($this->returnValue(array($this->extension => array('key' => $this->extension, 'clearcacheonload' => '1'))));

		$this->installMock->_set('listUtility', $this->listUtilityMock);
		$this->installMock->expects($this->once())->method('saveDefaultConfiguration')->with('dummy');
		$this->installMock->install('dummy');
	}

	/**
	 * @test
	 */
	public function uninstallCallsWriteNewExtensionList() {
		$this->installMock->expects($this->once())->method('writeNewExtensionList');
		$this->installMock->uninstall('dummy');
	}

	/**
	 * @test
	 */
	public function processDatabaseUpdatesCallsUpdateDbWithExtTablesSql() {
		$extKey = $this->createFakeExtension();
		$extPath = PATH_site . "typo3temp/$extKey/";
		$extTablesFile = $extPath . "ext_tables.sql";
		$fileContent = 'DUMMY TEXT TO COMPARE';
		file_put_contents($extTablesFile, $fileContent);
		$installMock = $this->getMock('Tx_Extensionmanager_Utility_Install', array('updateDbWithExtTablesSql'));
		$installMock->expects($this->once())->method('updateDbWithExtTablesSql')->with($this->stringStartsWith($fileContent));

		$installMock->processDatabaseUpdates($this->fakedExtensions[$extKey]);
	}

	/**
	 * @test
	 */
	public function processDatabaseUpdatesCallsUpdateDbWithExtTablesSqlIncludingCachingFrameworkTables() {
		$extKey = $this->createFakeExtension();
		$extPath = PATH_site . "typo3temp/$extKey/";
		$extTablesFile = $extPath . "ext_tables.sql";
		$fileContent = 'DUMMY TEXT TO COMPARE';
		file_put_contents($extTablesFile, $fileContent);
		$installMock = $this->getMock('Tx_Extensionmanager_Utility_Install', array('updateDbWithExtTablesSql'));
		$installMock->expects($this->once())->method('updateDbWithExtTablesSql')->with($this->stringContains('CREATE TABLE cf_cache_hash'));

		$installMock->processDatabaseUpdates($this->fakedExtensions[$extKey]);
	}

	/**
	 * @test
	 */
	public function processDatabaseUpdatesCallsImportStaticSql() {
		$extKey = $this->createFakeExtension();
		$extPath = PATH_site . "typo3temp/$extKey/";
		$extTablesFile = $extPath . "ext_tables_static+adt.sql";
		$fileContent = 'DUMMY TEXT TO COMPARE';
		file_put_contents($extTablesFile, $fileContent);
		$installMock = $this->getMock('Tx_Extensionmanager_Utility_Install', array('importStaticSql'));
		$installMock->expects($this->once())->method('importStaticSql')->with($fileContent);

		$installMock->processDatabaseUpdates($this->fakedExtensions[$extKey]);
	}

	/**
	 * @test
	 */
	public function writeNewExtensionListWritesToLocalconf(){
		$backupExtlist = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extList'];
		$localconfTestString = 'dummy1,dummy2';
		$t3libInstallMock = $this->getMock('t3lib_install', array('writeToLocalconf_control','setValueInLocalconfFile'));

		$installMock = $this->getMock('Tx_Extensionmanager_Utility_Install', array('getT3libInstallInstance'));
		$installMock->expects($this->once())->method('getT3libInstallInstance')->will($this->returnValue($t3libInstallMock));

		$t3libInstallMock->expects($this->exactly(2))->method('writeToLocalconf_control')->will($this->returnValue(array('dummyline')));
		$t3libInstallMock->expects($this->once())->method('setValueInLocalconfFile')
			->with(
				array('dummyline'),
				'$TYPO3_CONF_VARS[\'EXT\'][\'extList\']',
				$localconfTestString
			);
		$installMock->writeNewExtensionList($localconfTestString);
		$this->assertEquals('dummy1,dummy2', $GLOBALS['TYPO3_CONF_VARS']['EXT']['extList']);
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extList'] = $backupExtlist;
	}



}