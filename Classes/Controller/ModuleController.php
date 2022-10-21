<?php

namespace Bithost\PowermailFastexport\Controller;

use Bithost\PowermailFastexport\Domain\Repository\AnswerRepository;
use Bithost\PowermailFastexport\Domain\Repository\MailRepository;
use Bithost\PowermailFastexport\Exporter\CsvExporter;
use Bithost\PowermailFastexport\Exporter\XlsExporter;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\Powermail\Utility\StringUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;

/***
 *
 * This file is part of the "Powermail FastExport" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Marin <esteban.marin@bithost.ch>, Bithost GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***/

/**
 * ModuleController
 */
class ModuleController extends \In2code\Powermail\Controller\ModuleController
{
    public function __construct()
    {
        $this->renderingContext = GeneralUtility::makeInstance(RenderingContext::class);
    }

    /**
     * Export Action for XLS Files
     */
    public function exportXlsAction(): ResponseInterface
    {
        $mails = $this->getMailsAsArray();
        /** @var XlsExporter $xlsExporter */
        $xlsExporter = GeneralUtility::makeInstance(XlsExporter::class, $this->objectManager, $this->renderingContext);
        $fieldUids = GeneralUtility::trimExplode(
            ',',
            StringUtility::conditionalVariable($this->piVars['export']['fields'], ''),
            true
        );
        $fileName = StringUtility::conditionalVariable($this->settings['export']['filenameXls'], 'export.xls');

        return $this->exportResponse('application/vnd.ms-excel;', $fileName, $xlsExporter->export($mails, $fieldUids));
    }

    /**
     * get all mails as array
     *
     * @return array
     */
    private function getMailsAsArray(): array
    {
        if (!empty($this->settings['maxExecutionTime'])) {
            ini_set('max_execution_time', (int)$this->settings['maxExecutionTime']);
        }
        if (!empty($this->settings['memoryLimit'])) {
            ini_set('memory_limit', $this->settings['memoryLimit']);
        }

        /** @var MailRepository $mailRepository */
        $mailRepository = GeneralUtility::makeInstance(MailRepository::class);
        $dbMails = $mailRepository->findAllInPidRaw($this->id, $this->settings, $this->piVars);
        $mails = [];

        foreach ($dbMails as $mail) {
            $mails[$mail['uid']] = $mail;
        }

        /** @var AnswerRepository $answerRepository */
        $answerRepository = GeneralUtility::makeInstance(AnswerRepository::class);
        $answers = $answerRepository->findByMailUidsRaw(array_keys($mails));

        foreach ($answers as $answer) {
            if (!is_array($mails[$answer['mail']]['answers'])) {
                $mails[$answer['mail']]['answers'] = [];
            }
            $mails[$answer['mail']]['answers'][$answer['uid']] = $answer;
        }

        return $mails;
    }

    /**
     * Export Action for CSV Files
     *
     * @return ResponseInterface
     */
    public function exportCsvAction(): ResponseInterface
    {
        $mails = $this->getMailsAsArray();
        /** @var CsvExporter $csvExporter */
        $csvExporter = GeneralUtility::makeInstance(CsvExporter::class, $this->objectManager, $this->renderingContext);
        $fieldUids = GeneralUtility::trimExplode(
            ',',
            StringUtility::conditionalVariable($this->piVars['export']['fields'], ''),
            true
        );
        $fileName = StringUtility::conditionalVariable($this->settings['export']['filenameCsv'], 'export.csv');

        return $this->exportResponse('text/x-csv', $fileName, $csvExporter->export($mails, $fieldUids));
    }

    private function exportResponse(string $type, string $fileName, $body): ResponseInterface
    {
        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', $type . 'charset=utf-8')
            ->withAddedHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->withAddedHeader('Pragma', 'no-cache')
            ->withBody($this->streamFactory->createStream($body));
    }
}
