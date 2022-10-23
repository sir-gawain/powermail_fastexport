<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use In2code\Powermail\Controller\ModuleController;
use In2code\Powermail\Domain\Repository\AnswerRepository;
use In2code\Powermail\Domain\Repository\MailRepository;
defined('TYPO3') or die();


ExtensionManagementUtility::addUserTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:powermail_fastexport/Configuration/TSConfig/tsconfig.ts">'
);

/*
 * Add XCLASS definitions
 *
 */
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][ModuleController::class] = [
    'className' => \Bithost\PowermailFastexport\Controller\ModuleController::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][AnswerRepository::class] = [
    'className' => \Bithost\PowermailFastexport\Domain\Repository\AnswerRepository::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][MailRepository::class] = [
    'className' => \Bithost\PowermailFastexport\Domain\Repository\MailRepository::class
];
