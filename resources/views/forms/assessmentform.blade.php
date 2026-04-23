<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Protocol Assessment</title>
    <style>
        /* General Reset */
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
        }

        /* Page Layout - Long Bond Paper (8.5in x 13in) */
        .page {
            width: 8.5in;
            height: 13in;
            background-color: white;
            margin: 20px auto;
            padding: 0.5in;
            position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        @media print {
            @page {
                size: 8.5in 13in;
                margin: 0;
            }
            body { background-color: white; }
            .page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                height: 13in;
                padding: 0.5in;
                page-break-after: always;
            }
        }

        /* Typography */
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .small-text { font-size: 10pt; }
        .header-text { font-size: 10pt; }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black;
            margin: 0;
            table-layout: fixed;
        }

        td, th {
            border: 1px solid black;
            padding: 5px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* Header Specifics */
        .header-table td {
            padding: 2px 5px;
            vertical-align: middle;
            height: auto;
            border: 1px solid black;
        }

        /* Helper Classes */
        .v-middle { vertical-align: middle; }
        .h-center { text-align: center; }

        /* Logo Image */
        .logo-img {
            width: 65px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Footer Tracking - Strictly separated from tables */
        .footer-tracking {
            position: absolute;
            bottom: 0.5in;
            right: 0.5in;
            font-style: italic;
            text-align: right;
            font-weight: normal;
            font-size: 11pt;
            background-color: white;
        }

        /* Checkbox Styling */
        .checkbox-wrapper {
            display: flex;
            align-items: flex-start;
            margin-bottom: 5px;
        }
        .box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid black;
            margin-right: 6px;
            background: white;
            flex-shrink: 0;
            position: relative;
            top: 2px;
        }
        .cb-label {
            line-height: 1.2;
            font-size: 11pt;
        }

        /* Assessment Table specific */
        .assessment-table {
            margin-bottom: 40px; /* Forces distance from the footer tracking number */
        }
        .assessment-table th {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            background-color: white;
        }
        .section-title {
            background-color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="page">
        <table class="header-table">
            <colgroup>
                <col style="width: 15%;">
                <col style="width: 35%;">
                <col style="width: 30%;">
                <col style="width: 20%;">
            </colgroup>
            <tr>
                <td class="center" style="padding: 2px;">
                    <img src={{ asset('logo/bsu_logo.png')}} alt="Logo" class="logo-img">
                </td>
                <td class="header-text">Reference No.: BatStateU-FO-BERC-004</td>
                <td class="header-text">Effectivity Date:</td>
                <td class="header-text">Revision No.: 00</td>
            </tr>
        </table>

        <div class="center bold" style="border: 1px solid black; border-top: none; padding: 12px; font-size: 12pt; text-transform: uppercase; margin-top: -1px; margin-bottom: 15px;">
            STUDY PROTOCOL ASSESSMENT
        </div>

        <table style="margin-bottom: 15px;">
            <colgroup>
                <col style="width: 20%;">
                <col style="width: 30%;">
                <col style="width: 20%;">
                <col style="width: 30%;">
            </colgroup>
            <tr>
                <td>Title of the Study</td>
                <td colspan="3" style="height: 40px;">{{ $application->research_title }}</td>
            </tr>
            <tr>
                <td>BERC Code (To be Provided by BERC)</td>
                <td>{{ $application->protocol_code ?? 'Not Assigned Yet' }}</td>
                <td>Type of Review</td>
                <td>{{ $application->review_classification ?? 'Not Assigned Yet' }}</td>
            </tr>
            <tr>
                <td>Proponent</td>
                <td>{{ $application->name_of_researcher }}</td>
                <td>Institution</td>
                <td>{{ $application->institution }}</td>
            </tr>
            <tr>
                <td class="v-middle">Reviewer</td>
                <td class="v-middle">
                    Primary: &nbsp;&nbsp;
                    <span class="box"></span> Yes &nbsp;&nbsp;
                    <span class="box"></span> No
                </td>
                <td colspan="2"></td>
            </tr>
        </table>

        <div style="border: 1px solid black; padding: 10px; margin-bottom: 15px;">
            <div class="bold" style="margin-bottom: 10px;">
                Guide questions for reviewing the informed consent process and form
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 10px;">
                <span>Is it necessary to seek the informed consent of the participants?</span>

                <div class="checkbox-wrapper">
                    <span class="box">
                        @if($assessmentForm->is_consent_necessary === 'yes') ✔ @endif
                    </span>
                    <span class="cb-label">Yes</span>
                </div>

                <div class="checkbox-wrapper">
                    <span class="box">
                        @if($assessmentForm->is_consent_necessary === 'no') ✔ @endif
                    </span>
                    <span class="cb-label">No</span>
                </div>

                <div class="checkbox-wrapper">
                    <span class="box">
                        @if($assessmentForm->is_consent_necessary === 'unable') ✔ @endif
                    </span>
                    <span class="cb-label">Unable to Assess</span>
                </div>
            </div>

            <div style="margin-bottom: 10px; display: flex;">
                <span style="white-space: nowrap;">If NO. please explain:</span>
                <div style="border-bottom: 1px solid black; flex-grow: 1; margin-left: 10px; padding-left: 5px;">
                    {{ $assessmentForm->no_consent_explanation ?? '' }}
                </div>
            </div>

            <div>
                If YES, are the participants provided with sufficient information regarding:
            </div>
        </div>

        <table class="assessment-table" style="flex-grow: 1;">
            <colgroup>
                <col style="width: 35%;">
                <col style="width: 7%;">
                <col style="width: 7%;">
                <col style="width: 20%;">
                <col style="width: 31%;">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2">ASSESSMENT POINTS</th>
                    <th colspan="2">To be filled out by the PI<br><span style="font-weight: normal; font-size: 9pt;">Indicate if the study protocol contains the specified assessment point</span></th>
                    <th rowspan="2">Line and Page where it is found</th>
                    <th rowspan="2">REVIEWER COMMENTS</th>
                </tr>
                <tr>
                    <th>YES</th>
                    <th>NO</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-title">
                    <td colspan="5">1. SCIENTIFIC DESIGN</td>
                </tr>
                <tr>
                    @php
                        // Simple helper to check status safely
                        // Usage: check($items, 'QUESTION_NUMBER', 'STATUS_TO_CHECK')
                        function check($items, $qNum, $val) {
                            $qNum = (string)$qNum; // Ensure key is string
                            if (!$items->has($qNum)) return false;
                            // Case-insensitive check (handles 'Yes', 'yes', 'YES ')
                            return strcasecmp(trim($items[$qNum]->remark), $val) === 0;
                        }
                    @endphp
                    <td>Review of expected output viability
                        {{-- YES COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.1', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.1', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.1']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.1']))
                                @php $item = $items['1.1']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of results of previous animal/human studies showing known risks and benefits of intervention, including known adverse drug effects, in case of drug trials
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.2', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.2', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.2']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.2']))
                                @php $item = $items['1.2']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of appropriateness of design in view of objectives
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.3', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.3', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.3']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.3']))
                                @php $item = $items['1.3']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of appropriateness of sampling methods and techniques
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.4', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.4', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.4']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.4']))
                                @php $item = $items['1.4']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of justification of sample size
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.5', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.5', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.5']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.5']))
                                @php $item = $items['1.5']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer-tracking">Tracking No. ________________________</div>
    </div>

    <div class="page">
        <table class="assessment-table" style="flex-grow: 1;">
            <colgroup>
                <col style="width: 35%;">
                <col style="width: 7%;">
                <col style="width: 7%;">
                <col style="width: 20%;">
                <col style="width: 31%;">
            </colgroup>
            <tbody>
                <tr>
                    <td>Review of appropriateness of statistical methods to be used and how participant data will be summarized
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.6', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.6', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.6']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.6']))
                                @php $item = $items['1.6']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of appropriateness of statistical and non-statistical methods of data analysis
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.7', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.7', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.7']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.7']))
                                @php $item = $items['1.7']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of precision of criteria both for scientific merit and safety concerns; and of equitable selection
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.8', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.8', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.8']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.8']))
                                @php $item = $items['1.8']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    {{-- 1.9 --}}
                    <td>Review of precision of criteria both for scientific merit and safety concerns; and of justified selection</td> <td style="text-align: center;">
                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ check($items, '1.9', 'Yes') ? '✔' : '' }}
                            </span>
                        </div>
                    </td>
                    {{-- NO COLUMN --}}
                    <td style="text-align: center;">
                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ check($items, '1.9', 'No') ? '✔' : '' }}
                            </span>
                        </div>
                    </td>
                    <td>{{ $items['1.9']->line_page ?? '' }}</td>
                    <td style="vertical-align: top; padding: 5px;">
                        @if(isset($items['1.9']))
                            @php $item = $items['1.9']; @endphp

                            @if($item->synthesized_comments_action_required)
                                <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                {{ $item->synthesized_comments }}
                            @else
                                {{ $item->synthesized_comments ?? '' }}
                            @endif
                        @else
                            <span style="color: #ccc;">N/A</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    {{-- 1.10 --}}
                    <td>Review of criteria precision both for scientific merit and safety concerns</td> <td style="text-align: center;">
                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ check($items, '1.10', 'Yes') ? '✔' : '' }}
                            </span>
                        </div>
                    </td>
                    {{-- NO COLUMN --}}
                    <td style="text-align: center;">
                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ check($items, '1.10', 'No') ? '✔' : '' }}
                            </span>
                        </div>
                    </td>
                    <td>{{ $items['1.10']->line_page ?? '' }}</td>
                    <td style="vertical-align: top; padding: 5px;">
                        @if(isset($items['1.10']))
                            @php $item = $items['1.10']; @endphp

                            @if($item->synthesized_comments_action_required)
                                <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                {{ $item->synthesized_comments }}
                            @else
                                {{ $item->synthesized_comments ?? '' }}
                            @endif
                        @else
                            <span style="color: #ccc;">N/A</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Refusal to participate or discontinuance at any time will involve penalty or loss of benefits to which the subject is entitled?
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.11', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.11', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.11']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.11']))
                                @php $item = $items['1.11']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Statement that it involves research
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.12', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.12', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.12']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.12']))
                                @php $item = $items['1.12']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Approximate number of participants in the study
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.13', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.13', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.13']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.13']))
                                @php $item = $items['1.13']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Expected benefits to the community or to society, or contributions to scientific knowledge
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.14', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.14', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.14']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.14']))
                                @php $item = $items['1.14']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Description of post-study access to the study product or intervention that have been proven safe and effective
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.15', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.15', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.15']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.15']))
                                @php $item = $items['1.15']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Anticipated payment, if any, to the participant in the course of the study whether money or other forms of material goods, and if so, the kind and amount
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.16', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.16', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.16']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.16']))
                                @php $item = $items['1.16']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Anticipated expenses, if any, to the participant in the course of the study
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.17', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.17', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.17']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.17']))
                                @php $item = $items['1.17']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer-tracking">Tracking No. ________________________</div>
    </div>

    <div class="page">
        <table class="assessment-table" style="flex-grow: 1;">
            <colgroup>
                <col style="width: 35%;">
                <col style="width: 7%;">
                <col style="width: 7%;">
                <col style="width: 20%;">
                <col style="width: 31%;">
            </colgroup>
            <tbody>
                <tr>
                    <td>Statement that the study monitor(s), auditor(s), the BERC, and regulatory authorities will be granted direct access to participant's medical records for purposes ONLY of verification of clinical trial procedures and data
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '1.18', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '1.18', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['1.18']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['1.18']))
                                @php $item = $items['1.18']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr class="section-title">
                    <td colspan="5">2. CONDUCT OF STUDY</td>
                </tr>
                <tr>
                    <td>Review of specimen storage, access, disposal, and terms of use
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '2.1', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '2.1', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['2.1']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['2.1']))
                                @php $item = $items['2.1']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of CV and relevant certifications to ascertain capability to manage study related risks
                       <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '2.2', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '2.2', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['2.2']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['2.2']))
                                @php $item = $items['2.2']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of adequacy of qualified staff and infrastructures
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '2.3', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '2.3', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['2.3']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['2.3']))
                                @php $item = $items['2.3']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of length/extent of human participant involvement in the study
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '2.4', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '2.4', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['2.4']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['2.4']))
                                @php $item = $items['2.4']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr class="section-title">
                    <td colspan="5">3. ETHICAL CONSIDERATIONS</td>
                </tr>
                <tr>
                    <td>Review of management of conflict arising from financial, familial, or proprietary considerations of the PI, sponsor, or the study site
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.1', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.1', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.1']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.1']))
                                @php $item = $items['3.1']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of measures or guarantees to protect privacy and confidentiality of participant information as indicated by data collection methods including data protection plans
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.2', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.2', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.2']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.2']))
                                @php $item = $items['3.2']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of application of the principle of respect for persons, who may solicit consent, how and when it will be done; who may give consent especially in case of special populations like minors and those who are not legally competent to give consent, or indigenous people which require additional clearances
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.3', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.3', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.3']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.3']))
                                @php $item = $items['3.3']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer-tracking">Tracking No. ________________________</div>
    </div>

    <div class="page">
        <table class="assessment-table" style="flex-grow: 1;">
            <colgroup>
                <col style="width: 35%;">
                <col style="width: 7%;">
                <col style="width: 7%;">
                <col style="width: 20%;">
                <col style="width: 31%;">
            </colgroup>
            <tbody>
                <tr>
                    <td>Review of involvement of vulnerable study populations and impact on informed consent (see 3.3). Vulnerable groups include children, the elderly, ethnic and racial minority groups, the homeless, prisoners, people with incurable disease, people who are politically powerless, or junior members of a hierarchical group. Vulnerability must always be assessed in the context of the protocol and the participants.
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.4', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.4', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.4']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.4']))
                                @php $item = $items['3.4']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of manner of recruitment including appropriateness of identified recruiting parties
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.5', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.5', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.5']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.5']))
                                @php $item = $items['3.5']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of feasibility of obtaining assent vis à vis incompetence to consent; Review of applicability of the assent age brackets in children: 0-under 7: No assent; 7-under 12: Verbal Assent; 12-under 15: Simplified Assent Form; 15-under 18: Co-sign informed consent form with parents
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.6', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.6', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.6']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.6']))
                                @php $item = $items['3.6']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of level of risk and measures to mitigate these risks (including physical, psychological, social, economic), including plans for adverse event management; Review of justification for allowable use of placebo as detailed in the Declaration of Helsinki (as applicable)
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.7', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.7', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.7']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.7']))
                                @php $item = $items['3.7']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of potential direct benefit to participants; the potential to yield generalizable knowledge about the participants' condition/problem; non-material compensation to participant (health education or other creative benefits), where no clear, direct benefit from the project will be received by the participant
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.8', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.8', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.8']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.8']))
                                @php $item = $items['3.8']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review of amount and method of compensations, financial incentives, or reimbursement of study-related expenses
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.9', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.9', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.9']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.9']))
                                @php $item = $items['3.9']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer-tracking">Tracking No. ________________________</div>
    </div>

    <div class="page">
        <table class="assessment-table" style="margin-bottom: 20px;">
            <colgroup>
                <col style="width: 35%;">
                <col style="width: 7%;">
                <col style="width: 7%;">
                <col style="width: 20%;">
                <col style="width: 31%;">
            </colgroup>
            <tbody>
                <tr>
                    <td>Review of impact of the research on the community where the research occurs and/or to whom findings can be linked; including issues like stigma or draining of local capacity; sensitivity to cultural traditions, and involvement of the community in decisions about the conduct of study.
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.10', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.10', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.10']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.10']))
                                @php $item = $items['3.10']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
                <tr>
                    <td>Review in terms of collaborative study especially in case of multi-country/multi-institutional studies, including intellectual property rights, publication rights, information and responsibility sharing, transparency, and capacity building
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ check($items, '3.11', 'Yes') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        {{-- NO COLUMN --}}
                        <td style="text-align: center;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Logic: Is item 1 'No'? --}}
                                    {{ check($items, '3.11', 'No') ? '✔' : '' }}
                                </span>
                            </div>
                        </td>
                        </td>
                        <td>{{ $items['3.11']->line_page ?? '' }}</td>
                        <td style="vertical-align: top; padding: 5px;">
                            @if(isset($items['3.11']))
                                @php $item = $items['3.11']; @endphp

                                @if($item->synthesized_comments_action_required)
                                    <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                                    {{ $item->synthesized_comments }}
                                @else
                                    {{ $item->synthesized_comments ?? '' }}
                                @endif
                            @else
                                <span style="color: #ccc;">N/A</span>
                            @endif
                        </td>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="footer-tracking">Tracking No. ________________________</div>
    </div>

<script>
    window.onload = function() {
        // Wait for fonts and logos to render
        setTimeout(function() {
            window.print();
        }, 1000);

        // Listen for the completion of the print dialog
        window.onafterprint = function() {
            // Check if the window was opened via JS (standard for print buttons)
            if (window.history.length > 1) {
                // If there's a history, it's safer to just let the user go back
                // or stay on the page. Otherwise, try to close.
                window.close();
            } else {
                window.close();
            }
        };
    };
</script>
</body>
</html>
