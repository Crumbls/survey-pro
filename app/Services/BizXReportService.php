<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Report;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

/**
 * Used just to generate table schemas.  We use this in our code generator.
 */
class BizXReportService
{
    public static function createDefault(Report $report) {
        if ($report->data) {
            return;
        }

        $client = $report->client;

        if (!$client && $report->client_id) {
            $client = Client::find($report->client_id);
        }

        $tenant = $client->tenant;

        $report->data = [
            /*
            [
                'type' => 'center-logo',
                'data' => [],
            ],
            */
            [
                'type' => 'client-logo',
                'data' => [],
            ],
            [
                'type' => 'heading',
                'data' => [
                    'content' => $client->name.' Business Excellence Optimization Assessment Findings',
                    'level' => 'h1',
                    'alignment' => 'text-center',
                    'text_size' => 'text-xl',
                    'font_weight' => 'font-semibold',
                ],
            ],
            [
                'type' => 'paragraph',
                'data' => [
                    'content' => 'Thank you for taking the time to review the opportunities for improvement within '.$client->name.'. '.$tenant->name.' exists to support organizations to enable them to do what they do best each and every day and to make their lives a little easier. Below are the findings from the Business Excellence Optimization Assessment in addition to our recommendations on how to increase '.$client->name.'\'s value to their staff and stakeholders. The Business Excellence Optimization Assessment looks at 6 key components:',
        ],
    ],
    [
        'type' => 'pistons',
        'data' => [
        ],
    ],
    [
        'type' => 'paragraph',
        'data' => [
            'content' => 'The Business Excellence Optimization Assessment highlighted some areas for possible improvement. The assessment is a self-assessment that each member of the leadership team responded to. The scale was from 0 – 100 and scored in single digit increments. The scores were averaged across the team and color coded based on their average scores.',
        ],
    ],
    [
        'type' => 'paragraph',
        'data' => [
            'content' => 'Additionally, a common business challenge is getting the leadership team aligned on the organization\'s challenges, the root cause, and how they will work together to solve their challenges in order to optimize the company. In the "heatmap" that the assessment process generates we measure the alignment of leadership\'s responses to the assessment questions, as well as by each cylinder. This measurement allows the leadership team to see if the team interprets the questions the same and agrees on the challenges that they are facing.',
        ],
    ],
    [
        'type' => 'paragraph',
        'data' => [
            'content' => 'Below you will find the data based on the company\'s responses. The "Avg. by Question" is the average of the participant\'s responses by question based on the following scale: 85-100 is green, 70-84.99 is yellow, and 0-69.99 is red. The "Average by Cylinder" column is the average of all responses by cylinder using the same scale. The "Alignment by Question" is the standard deviation of the individual responses, or how "aligned" the team is. The "Alignment by Cylinder" is the average of the alignment by question summarized by cylinder. The scale for the alignment by question and cylinder is 0-20 green, 20.01-40 is yellow, and 40.01-100 red. In this case the lower the scale the better.',
        ],
    ],
    [
        'type' => 'charts',
        'data' => [
            'charts' => ['weighted', 'alignment'],
        ],
    ],
    [
        'type' => 'heatmap',
        'data' => [],
    ],
    [
        'type' => 'cylinder',
        'data' => [
            'rows' => [
                [
                    'cylinder' => 'Strategy',
                    'status' => 'pain',
                    'causes' => 'Test',
                    'recommendations' => '456',
                ],
                [
                    'cylinder' => 'Structure',
                    'status' => 'pain',
                    'causes' => '1',
                    'recommendations' => '2',
                ],
                [
                    'cylinder' => 'People Systems',
                    'status' => null,
                    'causes' => null,
                    'recommendations' => null,
                ],
                [
                    'cylinder' => 'Methods & Tools',
                    'status' => null,
                    'causes' => null,
                    'recommendations' => null,
                ],
                [
                    'cylinder' => 'Lateral Processes',
                    'status' => null,
                    'causes' => null,
                    'recommendations' => null,
                ],
                [
                    'cylinder' => 'Metrics',
                    'status' => 'pain',
                    'causes' => 'test',
                    'recommendations' => 'test',
                ],
                [
                    'cylinder' => 'Cyber',
                    'status' => null,
                    'causes' => null,
                    'recommendations' => null,
                ],
            ],
        ],
    ],
    [
        'type' => 'paragraph',
        'data' => [
            'content' => 'With the above comments and findings, we recommend that '.$client->name.' follow the steps outlined below for optimizing the business and increasing its value to all stakeholders. The recommendations follow the most urgent and highest impact areas based on the report-out discussion.',
        ],
    ],
    [
        'type' => 'heading',
        'data' => [
            'content' => 'Summary',
            'level' => 'h1',
            'alignment' => 'text-center',
            'text_size' => 'text-xl',
            'font_weight' => 'font-semibold',
        ],
    ],
    [
        'type' => 'paragraph',
        'data' => [
            'content' => 'We hope there are a number of opportunities highlighted here that '.$client->name.' can implement to strengthen and grow your business. If '.$client->name.' does not have the time or knowledge base to conduct these improvement activities, then '.$tenant->name.' is happy to partner and has additional information if needed.',
        ],
    ],
    [
        'type' => 'heading',
        'data' => [
            'content' => 'test',
            'level' => 'h1',
            'alignment' => 'text-center',
            'text_size' => 'text-xl',
            'font_weight' => 'font-semibold',
        ],
    ],
    [
        'type' => 'cylinders',
        'data' => [],
    ],
];
    }
}
