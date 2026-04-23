<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Informed Consent Form / Assent Form Evaluation Sheet</title>
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
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    @media print {
        @page {
        size: 8.5in 13in;
        margin: 0;
        }
        body {
        background-color: white;
        }
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
    .center {
        text-align: center;
    }
    .bold {
        font-weight: bold;
    }
    .italic {
        font-style: italic;
    }
    .header-text {
        font-size: 10pt;
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
        margin: 0;
        table-layout: fixed;
    }

    td,
    th {
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
    .v-middle {
        vertical-align: middle;
    }

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
    .box {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 1px solid black;
        margin-right: 6px;
        background: white;
        position: relative;
        top: 2px;
    }

    /* Assessment Table specific */
    .assessment-table th,
    .assessment-table td {
        background-color: white; /* Ensures no gray backgrounds */
    }
    </style>
</head>
<body>
    <div class="page">
    <table class="header-table">
        <colgroup>
        <col style="width: 15%" />
        <col style="width: 35%" />
        <col style="width: 30%" />
        <col style="width: 20%" />
        </colgroup>
        <tr>
        <td class="center" style="padding: 2px">
            <img src={{ asset('logo/bsu_logo.png') }} alt="Logo" class="logo-img" />
        </td>
        <td class="header-text">Reference No.: BatStateU-FO-BERC-004</td>
        <td class="header-text">Effectivity Date:</td>
        <td class="header-text">Revision No.: 00</td>
        </tr>
    </table>

    <div
        class="center bold"
        style="
        border: 1px solid black;
        border-top: none;
        padding: 10px;
        font-size: 11pt;
        margin-top: -1px;
        margin-bottom: 20px;
        "
    >
        INFORMED CONSENT FORM / ASSENT FORM EVALUATION SHEET
    </div>

    <table style="margin-bottom: 20px">
        <colgroup>
        <col style="width: 20%" />
        <col style="width: 30%" />
        <col style="width: 20%" />
        <col style="width: 30%" />
        </colgroup>
        <tr>
        <td>Title of the Study</td>
        <td colspan="3" style="height: 35px">{{ $application->research_title }}</td>
        </tr>
        <tr>
        <td>BERC Code (To be Provided by BERC)</td>
        <td>{{ $application->protocol_code ?? 'N/A' }}</td>
        <td>Type of Review</td>
        <td>{{ $application->review_classification ?? 'Not Classified'}}</td>
        </tr>
        <tr>
        <td>Proponent</td>
        <td>{{ $application->name_of_researcher ?? 'N/A' }}</td>
        <td>Institution</td>
        <td>{{ $application->institution ?? 'N/A' }}</td>
        </tr>
        <tr>
        <td class="v-middle">Reviewer</td>
        <td></td>
        <td colspan="2" class="v-middle center">
            Primary Reviewer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="box"></span> Yes &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="box"></span> No
        </td>
        </tr>
        <tr>
        <td
            colspan="4"
            class="bold"
            style="padding-top: 10px; padding-bottom: 10px"
        >
            Guide questions for reviewing the informed consent process and form
        </td>
        </tr>
        <tr>
        <td colspan="4" style="padding-bottom: 15px">
            Is it necessary to seek the informed consent of the participants?<br /><br />

            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="box"></span>
            Unable to Assess

            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="box">✓</span>
            Yes

            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="box"></span>
            No
            <br /><br />

            <u>If NO, please explain.</u><br /><br />
        </td>
        </tr>
        <tr>
        <td colspan="4" style="padding-bottom: 15px">
            <u>If YES</u>, are the participants provided with sufficient
            information<br /><br />
            regarding:
        </td>
        </tr>
    </table>

    <table class="assessment-table" style="flex-grow: 1; margin-bottom: 50px">
        <colgroup>
        <col style="width: 35%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
        <col style="width: 20%" />
        <col style="width: 31%" />
        </colgroup>
        <thead>
        <tr>
            <th class="center v-middle bold">Essential Elements</th>
            <th colspan="3" class="center v-middle bold">
            To be filled out by the PI
            </th>
            <th class="center v-middle bold">REVIEWER<br />COMMENTS</th>
        </tr>
        <tr>
            <td class="center v-middle bold">(as applicable to the study)</td>
            <td colspan="3"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" class="center v-middle" style="font-size: 10pt">
            Indicate if the ICF has the<br />specified element
            </td>
            <td class="center v-middle" style="font-size: 10pt">
            Line and<br />Page where<br />it is found
            </td>
            <td></td>
        </tr>
        @php
            // Helper to safely check remarks (Case-Insensitive)
            function check($items, $qNum, $val) {
                $key = (string)$qNum;

                // 1. Check if item exists in the collection
                if (!isset($items[$key])) return false;

                // 2. Check if remark matches (ignoring case/spaces)
                //    Your model uses 'remark', so $items[$key]->remark is correct.
                return strcasecmp(trim($items[$key]->remark), $val) === 0;
            }
        @endphp
        <tr>
            <td></td>
            <td class="center v-middle bold">YES</td>
            <td class="center v-middle bold">NO</td>
            <td></td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Purpose of the study?</td>
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.1', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.1', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.1']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.1']))
                    @php $item = $items['4.1']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>Expected duration of participation?</td>
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.2', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.2', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.2']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.2']))
                    @php $item = $items['4.2']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>Procedures to be carried out?</td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.3', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.3', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.3']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.3']))
                    @php $item = $items['4.3']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>Discomforts and inconveniences?</td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.4', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.4', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.4']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.4']))
                    @php $item = $items['4.4']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>Risks (including possible<br />discrimination)?</td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.5', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.5', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.5']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.5']))
                    @php $item = $items['4.5']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>Random assignment to the trial<br />treatments?</td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.6', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.6', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.6']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.6']))
                    @php $item = $items['4.6']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
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
    <table class="assessment-table" style="flex-grow: 1; margin-bottom: 50px">
        <colgroup>
        <col style="width: 35%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
        <col style="width: 20%" />
        <col style="width: 31%" />
        </colgroup>
        <tbody>
        <tr>
            <td>Benefits to the participants?</td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.7', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.7', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.7']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.7']))
                    @php $item = $items['4.7']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>Alternative treatments procedures?</td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.8', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.8', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.8']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.8']))
                    @php $item = $items['4.8']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Compensation and / or medical<br />treatments in case of injury?
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.9', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.9', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.9']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.9']))
                    @php $item = $items['4.9']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Who to contact for pertinent<br />questions and or for<br /><br />assistance
            in a research-related<br />injury?
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.10', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.10', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.10']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.10']))
                    @php $item = $items['4.10']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Refusal to participate or<br />discontinuance at any time will<br /><br />Involve
            penalty or loss of benefits<br />to which the subject is entitled?
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.11', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.11', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.11']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.11']))
                    @php $item = $items['4.11']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>Statement that it involves research</td>
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.12', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.12', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.12']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.12']))
                    @php $item = $items['4.12']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>Approximate number of<br />participants in the study</td>
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.13', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.13', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.13']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.13']))
                    @php $item = $items['4.13']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Expected benefits to the<br />community or to society, or<br />contributions
            to scientific<br />knowledge
            </td>
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.14', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.14', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.14']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.14']))
                    @php $item = $items['4.14']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Description of post-study access<br />to the study product or<br />intervention
            that have been<br />proven safe and effective
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.15', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.15', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.15']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.15']))
                    @php $item = $items['4.15']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Anticipated payment, if any, to<br />the participant in the course
            of<br />the study; whether money or<br />other forms of material
            goods,<br />and if so, the kind and amount
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.16', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.16', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.16']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.16']))
                    @php $item = $items['4.16']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Anticipated expenses, if any, to<br />the participant in the
            course of<br />the study
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.17', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.17', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.17']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.17']))
                    @php $item = $items['4.17']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Statement that the study<br />monitor(s), auditor(s), the<br />BERC,
            and regulatory<br />authorities will be granted direct<br />access
            to participant's medical<br />records for purposes ONLY of<br />verification
            of clinical trial<br />procedures and data
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.18', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.18', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.18']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.18']))
                    @php $item = $items['4.18']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Statement describing extent of<br />participant's right to
            access<br />his/her records (or lack thereof<br />vis à vis
            pending request for
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.19', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.19', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.19']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.19']))
                    @php $item = $items['4.19']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
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
    <table class="assessment-table" style="flex-grow: 1; margin-bottom: 20px">
        <colgroup>
        <col style="width: 35%" />
        <col style="width: 7%" />
        <col style="width: 7%" />
        <col style="width: 20%" />
        <col style="width: 31%" />
        </colgroup>
        <tbody>
        <tr>
            <td>approval of non or partial<br />disclosure)</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>
            Description of policy regarding<br />the use of genetic tests
            and<br />familial genetic information, and<br />the precautions in
            place to<br />prevent disclosure of results to<br />immediate
            family relative or to<br />others without consent of the<br />participant
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.20', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.20', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.20']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.20']))
                    @php $item = $items['4.20']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Possible direct or secondary use<br />of participant's medical
            records<br />and biological specimens taken<br />in the course of
            clinical care or<br />in the course of this study
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.21', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.21', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.21']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.21']))
                    @php $item = $items['4.21']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Plans to destroy collected<br />biological specimen at the end<br />of
            the study; if not, details about<br />storage (duration, type
            of<br />storage facility, location, access<br />information) and
            possible future<br />use; affirming participant's right<br />to
            refuse future use, refuse<br />storage, or have the materials<br />destroyed
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.22', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.22', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.22']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.22']))
                    @php $item = $items['4.22']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Plans to develop commercial<br />products from biological<br />specimens
            and whether the<br />participant will receive<br />monetary or
            other benefit from<br />such development.
            </td>
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.23', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.23', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.23']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.23']))
                    @php $item = $items['4.23']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        <tr>
            <td>
            Statement that the<br />BERC (specify) has<br />approved the
            study, and may be<br />reached through the following<br />contact
            for information<br />regarding rights of study<br />participants,
            including<br />grievances and complaints:<br /><br />BERC
            Chairperson<br />
            </td>
            <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ check($items, '4.24', 'Yes') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
                {{-- NO COLUMN --}}
                <td style="text-align: center;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{-- Logic: Is item 1 'No'? --}}
                            {{ check($items, '4.24', 'No') ? '✔' : '' }}
                        </span>
                    </div>
                </td>
            </td>
            <td>{{ $items['4.24']->line_page ?? '' }}</td>
            <td style="vertical-align: top; padding: 5px;">
                @if(isset($items['4.24']))
                    @php $item = $items['4.24']; @endphp

                    {{--
                    Instead of checking 'synthesized_comments_action_required',
                    we check if the 'final_comment' contains the "ACTION REQUIRED" string
                    OR check the boolean property if you set it in the controller.
                    --}}

                    @if(str_contains($item->final_comment, 'ACTION REQUIRED:'))
                        <strong style="color: #dc2626; text-transform: uppercase;">Action Required:</strong>
                        {{ str_replace('ACTION REQUIRED: ', '', $item->final_comment) }}
                    @else
                        {{ $item->final_comment ?? '' }}
                    @endif
                @else
                    <span style="color: #ccc;">N/A</span>
                @endif
            </td>
        </td>
        </tr>
        </tbody>
    </table>

    <table style="flex-grow: 1; margin-bottom: 20px">
        <colgroup>
        <col style="width: 40%" />
        <col style="width: 30%" />
        <col style="width: 30%" />
        </colgroup>
        <tr>
        <th class="center v-middle">PRIMARY REVIEWER</th>
        <th class="center v-middle">DATE: dd/mmm/yyyy</th>
        <th class="center v-middle">DATE: dd/mmm/yyyy</th>
        </tr>
        <tr>
        <td>1.</td>
        <td></td>
        <td></td>
        </tr>
        <tr>
        <td>2.</td>
        <td></td>
        <td></td>
        </tr>
    </table>

    <table style="flex-grow: 1; margin-bottom: 50px">
        <colgroup>
        <col style="width: 40%" />
        <col style="width: 30%" />
        <col style="width: 30%" />
        </colgroup>
        <tr>
        <th class="center v-middle">
            Names: Other reviewer<br />members of full board review.
        </th>
        <th class="center v-middle">DATE: dd/mmm/yyyy</th>
        <th class="center v-middle">DATE: dd/mmm/yyyy</th>
        </tr>
        <tr>
        <td>1.</td>
        <td></td>
        <td></td>
        </tr>
    </table>

    <div class="footer-tracking">Tracking No. ________________________</div>
    </div>

    <div class="page">
    <table style="margin-bottom: 50px">
        <colgroup>
        <col style="width: 40%" />
        <col style="width: 30%" />
        <col style="width: 30%" />
        </colgroup>
        <tr>
        <td style="height: 45px">2.</td>
        <td></td>
        <td></td>
        </tr>
        <tr>
        <td style="height: 45px">3.</td>
        <td></td>
        <td></td>
        </tr>
        <tr>
        <td style="height: 45px">4.</td>
        <td></td>
        <td></td>
        </tr>
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
