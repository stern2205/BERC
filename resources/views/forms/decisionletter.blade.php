<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decision Letter - {{ $application->protocol_code }}</title>
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
            line-height: 1.4;
        }

        /* Page Layout - Long Bond Paper (8.5in x 13in) */
        .page {
            width: 8.5in;
            min-height: 13in; /* Changed to min-height for content overflow */
            background-color: white;
            margin: 20px auto;
            padding: 0.7in; /* Increased padding slightly for formal look */
            position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        @media print {
            @page {
                size: 8.5in 13in;
                margin: 0.5; /* Set margin here for native header/footer management */
            }
            body { background-color: white; }
            .page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: unset;
                padding: 0;
                page-break-after: auto;
            }
            .no-print { display: none; }
        }

        /* Typography */
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .header-text { font-size: 10pt; }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid black;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        td, th {
            border: 1px solid black;
            padding: 8px 10px;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* Header Specifics */
        .header-table td {
            padding: 2px 5px;
            vertical-align: middle;
            border: 1px solid black;
        }

        .logo-img {
            width: 65px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Decision Letter Specific Content */
        .letter-content {
            margin-top: 30px;
        }

        .letter-content p {
            margin-top: 0;
            margin-bottom: 15px;
            text-align: justify;
        }

        .indented-code {
            margin-left: 30px;
            margin-bottom: 20px;
        }

        ul {
            margin-top: 5px;
            margin-bottom: 20px;
            padding-left: 40px;
        }

        /* Table Break Logic */
        .comments-table {
            page-break-inside: auto;
        }
        .comments-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .comments-table thead {
            display: table-header-group; /* Repeats header if table spans pages */
        }

        /* Ensure the section headers look distinct and don't get stranded at the bottom of a page */
        .comments-table tr[style*="background-color: #e9ecef"] {
            page-break-after: avoid;
        }

        .footer-tracking {
            position: fixed;
            /* Increase these values to move it away from the 'unprintable' edge */
            right: 0;

            /* Reset positioning from previous turns */
            left: auto !important;
            width: auto;

            /* Styling */
            font-size: 9pt;
            font-style: italic;
            color: #444;
            text-align: right !important; /* Force text to the right */
            background: transparent;
            z-index: 9999;
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
                <td class="center">
                    <img src="{{ asset('logo/bsu_logo.png') }}" alt="Logo" class="logo-img">
                </td>
                <td class="header-text">Reference No.: BatStateU-FO-BERC-005</td>
                <td class="header-text">Effectivity Date: {{ \Carbon\Carbon::now()->format('F d, Y') }}</td>
                <td class="header-text">Revision No.: {{ isset($version) && $version > 0 ? str_pad($version, 2, '0', STR_PAD_LEFT) : '00' }}</td>
            </tr>
        </table>

        <div class="center bold" style="border: 1px solid black; border-top: none; padding: 8px; font-size: 12pt; text-transform: uppercase; margin-top: -21px; margin-bottom: 30px;">
            DECISION LETTER
        </div>

        <div class="letter-content">
            <p>{{ \Carbon\Carbon::parse($decisionLetter->letter_date)->format('F d, Y') }}</p>

            <p style="margin-bottom: 20px;">
                <span class="bold">{{ strtoupper($decisionLetter->proponent) }}</span><br>
                {{ $decisionLetter->designation }}<br>
                {{ $decisionLetter->institution }}<br>
                {{ $decisionLetter->address }}
            </p>

            <p>
                <span class="bold">RE:</span> {{ $decisionLetter->title }}
            </p>

            <div class="indented-code bold">
                Protocol Code: {{ $application->protocol_code }}
                @if(isset($version) && $version > 0)
                    <br>Version: {{ $version }}
                @endif
            </div>

            <p style="margin-bottom: 25px;">
                <span class="bold">Subject:</span> {{ $decisionLetter->subject }}
            </p>

            <p>
                Dear <span class="italic">{{ $decisionLetter->dear_name }}</span>:
            </p>

            <p>
                We wish to inform you that the Batangas State University Research Ethics Committee (BERC) reviewed your study protocol during its regular meeting. Your study has been assigned the protocol code <span class="bold">{{ $application->protocol_code }}</span>, which should be used in all future communications related to this study.
            </p>

            @if(isset($version) && $version > 0)
            <p class="italic">
                Please note that this document serves as the official evaluation for <strong>Version {{ $version }}</strong> of the submitted protocol.
            </p>
            @endif

            <p>
                This is to acknowledge receipt of your request and the following support documents dated
                {{ $decisionLetter->support_date ? \Carbon\Carbon::parse($decisionLetter->support_date)->format('F d, Y') : '_________________' }}:
            </p>

            <ul>
                @php
                    $docArray = is_string($decisionLetter->documents) ? json_decode($decisionLetter->documents, true) : $decisionLetter->documents;
                @endphp
                @if(!empty($documents))
                    @foreach($documents as $doc)
                        @if(!empty(trim($doc)))
                            <li>{{ trim($doc) }}</li>
                        @endif
                    @endforeach
                @else
                    <li>Revised Study Protocol</li>
                    <li>Revised Informed Consent Form</li>
                @endif
            </ul>

            <p>
                @if($decisionLetter->decision_status === 'approved')
                    As a result of the review, we are pleased to inform you that your study protocol has been <strong>APPROVED</strong>. You may proceed with your research; however, please note the recommended improvements and/or clarifications summarized below for your guidance.
                @elseif($decisionLetter->decision_status === 'minor_revision')
                    As a result of the review, your study protocol requires <strong>MINOR REVISIONS</strong>. Please address the recommended revisions and clarifications summarized below before resubmitting your application.
                @elseif($decisionLetter->decision_status === 'major_revision')
                    As a result of the review, your study protocol requires <strong>MAJOR REVISIONS</strong>. Please thoroughly address the recommended revisions and clarifications summarized below before resubmitting your application.
                @elseif($decisionLetter->decision_status === 'rejected')
                    As a result of the review, we regret to inform you that your study protocol has been <strong>DISAPPROVED</strong>. The specific findings and reasons leading to this decision are summarized below.
                @else
                    As a result of the review, the following recommendations and clarifications have been generated:
                @endif
            </p>

            <table class="comments-table">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="width: 35%; text-align: center;">Points for Revision</th>
                        <th style="width: 65%; text-align: center;">Recommendations / Findings</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentSection = null; @endphp

                    @forelse($actionItems as $item)
                        {{-- Check if we need to display a new Section Header --}}
                        @if($currentSection !== $item->section)
                            @php $currentSection = $item->section; @endphp
                            <tr style="background-color: #e9ecef;">
                                <td colspan="2" style="font-weight: bold; text-transform: uppercase; font-size: 10pt; letter-spacing: 1px; padding: 10px;">
                                    {{ $currentSection }}
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td class="bold" style="padding-left: 15px;">{{ $item->points }}</td>
                            <td>{!! nl2br(e($item->synthesizedComments)) !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="center italic" style="padding: 20px;">
                                No specific points marked for action required.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <p style="margin-top: 30px;">Very truly yours,</p>

                <div style="margin-top: 10px; position: relative; display: inline-block; min-width: 250px;">

                    @if($chairUser && $chairUser->e_signature)
                        <img src="{{ route('signature.view_specific', $chairUser->id) }}"
                            alt="Chair Signature"
                            style="position: absolute;
                                    bottom: 20px; /* Adjust this to sit the signature perfectly on the line */
                                    left: 10px;
                                    max-height: 85px;
                                    width: auto;
                                    z-index: 1;
                                    pointer-events: none;
                                    mix-blend-mode: multiply; /* Removes white background for realism */
                            ">
                    @endif

                    <div style="position: relative; z-index: 2; margin-top: 40px;">
                        <span class="bold" style="text-transform: uppercase; border-bottom: 1px solid black; padding-bottom: 2px; display: inline-block; min-width: 200px;">
                            {{ $chairUser ? $chairUser->name : 'NAME OF BERC CHAIR' }}
                        </span><br>
                        <span style="display: inline-block; margin-top: 5px;">Chair, BERC</span>
                    </div>
                </div>

            <div class="footer-tracking">Tracking No. {{ $application->protocol_code }}</div>
        </div>
    </div>

</body>
<script>
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 1000);

        window.onafterprint = function() {
            window.close();
        };
    };
</script>
</html>
