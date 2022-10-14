<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "powermail_fastexport"
 *
 * Auto generated by Extension Builder 2016-06-17
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Powermail FastExport',
    'description' => 'Extend powermail for faster export to .xlsx / .csv files.
This is useful if you have many records to be exported.',
    'category' => 'be',
    'author' => 'Markus Mächler, Esteban Marin',
    'author_email' => 'markus.maechler@bithost.ch, esteban.marin@bithost.ch',
    'state' => 'stable',
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'powermail' => '9.0.0-10.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
