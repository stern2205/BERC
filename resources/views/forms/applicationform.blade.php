<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application for Ethics Review Protocol</title>
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

        /* Header Specifics - MINIMIZED */
        .header-table td {
            padding: 2px 5px;
            vertical-align: middle;
            height: auto;
        }

        /* Helper Classes */
        .v-middle { vertical-align: middle; }
        .h-center { text-align: center; }

        /* Section Header Box - MINIMIZED PADDING */
        .section-header-box {
            border: 1px solid black;
            border-bottom: 1px solid black;
            padding: 4px 8px;
            background: white;
            font-weight: bold;
            margin-top: -1px;
            font-size: 11pt;
            position: relative;
            z-index: 1;
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

        /* Grid for Study Types */
        .study-grid {
            display: flex;
            justify-content: space-between;
        }
        .study-col { width: 49%; }

        /* Logo Image */
        .logo-img {
            width: 65px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Footer Tracking */
        .footer-tracking {
            position: absolute;
            bottom: 0.5in;
            right: 0.5in;
            font-style: italic;
            text-align: right;
            font-weight: normal;
            font-size: 11pt;
        }

        /* Utilities */
        .connected-table { margin-top: -1px; }

        /* Column Configuration */
        /* c1 + c2 = 50% | c3 starts at 50% */
        .c1 { width: 22%; }
        .c2 { width: 28%; }
        .c3 { width: 15%; }
        .c4 { width: 15%; }
        .c5 { width: 20%; }

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
                    <img src="{{ asset('logo/bsu_logo.png') }}" alt="Logo" class="logo-img">
                </td>
                <td class="header-text">Reference No.: BatStateU-FO-BERC-001</td>
                <td class="header-text">Effectivity Date:</td>
                <td class="header-text">Revision No.: 00</td>
            </tr>
        </table>

        <div class="center bold" style="border: 1px solid black; border-top: none; padding: 12px; font-size: 12pt; text-transform: uppercase; margin-top: -1px;">
            Application for Ethics Review of a New Protocol
        </div>

        <div class="italic small-text" style="padding: 10px 0;">
            Instructions to the Researcher: Please accomplish this form and ensure that you have included in your submission the documents that you checked in Section 3 Checklist of Documents.
        </div>

        <div class="section-header-box" style="margin-top: 0; border-top: 1px solid black;">
            1. General Information
        </div>

        <table class="connected-table">
            <colgroup>
                <col class="c1">
                <col class="c2">
                <col class="c3">
                <col class="c4">
                <col class="c5">
            </colgroup>

            <tr>
                <td style="padding: 8px;">Title of the Study</td>
                <td colspan="4" style="padding: 8px;">
                    {{ $application->research_title ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td style="height: 35px; padding-left: 8px; vertical-align: middle;">
                    BERC Code (To be Provided by BERC)
                </td>

                <td style="vertical-align: middle;">
                    {{ $application->protocol_code ?? 'N/A' }}
                </td>

                <td style="padding-left: 8px; vertical-align: middle;">
                    Study Site
                </td>

                <td colspan="2" style="vertical-align: middle;">
                    {{ $application->study_site ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td rowspan="3" class="v-middle">Name of Researcher</td>
                <td rowspan="3" class="v-middle">
                    {{ $application->name_of_researcher ?? 'N/A' }}
                </td>

                <td rowspan="4" class="v-middle" style="background-color: #fff;">
                    Contact<br>Information
                </td>

                <td class="v-middle">Tel No:</td>
                <td class="v-middle">
                    {{ $application->tel_no ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td class="v-middle">Mobile Number</td>
                <td class="v-middle">
                    {{ $application->mobile_no ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td class="v-middle">Fax No:</td>
                <td class="v-middle">
                    {{ $application->fax_no ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td class="v-middle">Co-Researcher (if any)</td>
                <td class="v-middle">
                    @if(!empty($application->co_researchers) && is_array($application->co_researchers))
                        {{ implode(', ', $application->co_researchers) }}
                    @else
                        None
                    @endif
                </td>

                <td class="v-middle">Email:</td>
                <td class="v-middle">
                    {{ $application->email ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td style="height: 40px;" class="v-middle">Institution</td>
                <td colspan="4" class="v-middle" style="padding-left: 8px;">
                    {{ $application->institution ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td style="height: 40px;" class="v-middle">Address of Institution</td>
                <td colspan="4" class="v-middle" style="padding-left: 8px;">
                    {{ $application->institution_address ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td rowspan="2" class="v-middle">Type of Study</td>

                <td colspan="4" style="padding: 15px 10px;">
                    <div class="study-grid">
                        <div class="study-col">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->type_of_study === 'Clinical Trial (Sponsored)' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Clinical Trial (Sponsored)</span>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->type_of_study === 'Clinical Trials (Researcher-initiated)' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Clinical Trials (Researcher-initiated)</span>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->type_of_study === 'Health Operations Research (Health Programs and Policies)' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Health Operations Research<br>(Health Programs and Policies)</span>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->type_of_study === 'Social/ Behavioral Research' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Social/ Behavioral Research</span>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->type_of_study === 'Public Health-Epidemiologic Research' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Public Health/ Epidemiologic Research</span>
                            </div>
                        </div>

                        <div class="study-col">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->type_of_study === 'Biomedical research (Retrospective Prospective and diagnostic studies)' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Biomedical research (Retrospective<br>Prospective and diagnostic studies)</span>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->type_of_study === 'Stem Cell Research' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Stem Cell Research</span>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->type_of_study === 'Genetic Research' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Genetic Research</span>
                            </div>

                            @php
                                // Define the list of standard studies
                                $standardStudies = [
                                    'Clinical Trial (Sponsored)',
                                    'Clinical Trials (Researcher-initiated)',
                                    'Health Operations Research (Health Programs and Policies)',
                                    'Social-Behavioral Research',
                                    'Public Health-Epidemiologic Research',
                                    'Biomedical research (Retrospective / Prospective and diagnostic studies)',
                                    'Stem Cell Research'
                                ];

                                // Check if the value is custom (not in the list) and not empty
                                $isCustomStudy = !in_array($application->type_of_study, $standardStudies) && !empty($application->type_of_study);
                            @endphp

                            <div class="checkbox-wrapper" style="display: flex; align-items: center; white-space: nowrap;">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- Show checkmark if the study type is custom --}}
                                    {{ $isCustomStudy ? '✔' : '' }}
                                </span>

                                <span class="cb-label" style="display: flex; align-items: baseline;">
                                    Others:
                                    <span style="border-bottom: 1px solid black; min-width: 175px; margin-left: 5px; text-align: center; display: inline-block;">
                                        {{-- Use unescaped tags for the non-breaking space, but make sure to escape the database value for security --}}
                                        {!! $isCustomStudy ? e($application->type_of_study) : '&nbsp;' !!}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="border-top: 1px solid black; padding: 25px 10px;">
                    <div style="display: flex; gap: 30px;">

                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ $application->type_of_study1 === 'Multicenter (International)' ? '✔' : '' }}
                            </span>
                            <span class="cb-label">Multicenter (International)</span>
                        </div>

                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ $application->type_of_study1 === 'Multicenter (National)' ? '✔' : '' }}
                            </span>
                            <span class="cb-label">Multicenter (National)</span>
                        </div>

                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ $application->type_of_study1 === 'Single Site' ? '✔' : '' }}
                            </span>
                            <span class="cb-label">Single Site</span>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="v-middle">Source of Funding</td>
                <td colspan="4" style="padding: 15px 10px;">
                    <div class="study-grid">
                        <div class="study-col" style="width: 38%;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->source_of_funding === 'Self-funded' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Self-funded</span>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->source_of_funding === 'Government-Funded' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Government-Funded</span>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->source_of_funding === 'Scholarship/Research Grant' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Scholarship/Research Grant</span>
                            </div>
                        </div>

                        <div class="study-col" style="width: 62%;">
                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->source_of_funding === 'Sponsored by a Pharmaceutical Company' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Sponsored by a Pharmaceutical Company</span>
                            </div>

                            <div style="margin-left: 25px; display: flex; align-items: flex-end; margin-bottom: 5px;">
                                <span style="font-size: 10pt; margin-right: 5px;">Specify:</span>
                                <div style="border-bottom: 1px solid black; flex-grow: 1; font-size: 10pt; text-align: center;">
                                    {{ $application->source_of_funding === 'Sponsored by a Pharmaceutical Company' ? $application->source_of_funding_others : '' }}
                                </div>
                            </div>

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{ $application->source_of_funding === 'Institution-Funded' ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Institution-Funded</span>
                            </div>

                            @php
                                // Define your standard list of categories
                                $standardFunding = [
                                    'Self-funded',
                                    'Government-Funded',
                                    'Scholarship/Research Grant',
                                    'Sponsored by a Pharmaceutical Company',
                                    'Institution-Funded'
                                ];

                                // Determine if the current value is an "Other" value
                                $isOthers = !in_array($application->source_of_funding, $standardFunding) && !empty($application->source_of_funding);
                            @endphp

                            <div class="checkbox-wrapper">
                                <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                    {{-- If it's not in the list, check the box --}}
                                    {{ $isOthers ? '✔' : '' }}
                                </span>
                                <span class="cb-label">Others</span>
                            </div>

                            <div style="margin-left: 26px; border-bottom: 1px solid black; width: 90%; height: 16px; font-size: 10pt; text-align: center; vertical-align: bottom;">
                                {{-- If it's not in the list, display the value stored in source_of_funding --}}
                                {{ $isOthers ? $application->source_of_funding : '' }}
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td style="padding-top: 15px;">Duration of the Study</td>
                <td style="padding-top: 15px;">
                    <div style="margin-bottom: 25px;">Start date: {{ $application->study_start_date }}</div>
                    <div>End date: {{ $application->study_end_date }}</div>
                </td>
                <td class="v-middle" style="height: 40px; padding-left: 8px;">
                    No. of Study Participants
                </td>
                <td colspan="2" class="v-middle h-center">
                    {{ $application->study_participants ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <td colspan="2" style="height: 60px; padding-left: 8px;" class="v-middle">
                    Has the Research undergone a Technical Review?
                </td>
                <td colspan="3" class="v-middle">
                    <div style="display: flex; flex-direction: column; gap: 5px; margin-left: 10px; padding: 5px 0;">
                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ $application->technical_review ? '✔' : '' }}
                            </span>
                            <span class="cb-label">Yes (please attach technical review results)</span>
                        </div>

                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ !$application->technical_review ? '✔' : '' }}
                            </span>
                            <span class="cb-label">No</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer-tracking">
            Tracking No. <span style="border-bottom: 1px solid black; padding: 0 10px; display: inline-block; min-width: 50px; text-align: center;">{{ $application->id }}</span>
        </div>
    </div>

    <div class="page">
        <table class="connected-table">
             <colgroup>
                <col class="c1">
                <col class="c2">
                <col class="c3">
                <col class="c4">
                <col class="c5">
            </colgroup>
            <tr>
                <td colspan="2" class="v-middle" style="height: 50px; padding-left: 8px;">
                    Has the research been submitted to another BERC?
                </td>
                <td colspan="3" class="v-middle">
                    <div style="display: flex; gap: 50px; margin-left: 10px;">
                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ $application->has_been_submitted_to_another_berc === true || $application->has_been_submitted_to_another_berc === 1 ? '✔' : '' }}
                            </span>
                            <span class="cb-label">Yes</span>
                        </div>

                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ $application->has_been_submitted_to_another_berc === false || $application->has_been_submitted_to_another_berc === 0 ? '✔' : '' }}
                            </span>
                            <span class="cb-label">No</span>
                        </div>
                    </div>
                </td>
            </tr>
        </tr>
        <tr>
            <td colspan="5" class="section-header-box" style="background-color: #ffffff; font-weight: bold; border-bottom: 1px solid black;">
                2. Brief Description of the Study
            </td>
        </tr>

        <tr>
            <td colspan="5" style="padding: 15px; height: 200px; vertical-align: top; text-align: justify; line-height: 1.5;">
                {{ $application->brief_description ?? 'No description provided.' }}
            </td>
        </tr>
        </table>

        <div class="section-header-box">
            3. Checklist of Documents
        </div>
        <table class="connected-table">
            <colgroup>
                <col style="width: 50%;">
                <col style="width: 50%;">
            </colgroup>
            <tr>
                <td class="bold" style="text-align: left; padding-left: 10px; border-right: none; border-bottom: none;">Basic Requirements</td>
                <td class="bold" style="text-align: left; padding-left: 10px; border-left: none; border-bottom: none;">Supplementary Documents</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-right: none; border-top: none; vertical-align: top;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $application->basicRequirements->where('type', 'letter_request')->isNotEmpty() ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Letter request for review</span>
                    </div>

                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $application->basicRequirements->where('type', 'endorsement_letter')->isNotEmpty() ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Endorsement/Referral Letter</span>
                    </div>

                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $application->basicRequirements->where('type', 'full_proposal')->isNotEmpty() ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Full proposal/study protocol</span>
                    </div>

                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $application->basicRequirements->where('type', 'technical_review_approval')->isNotEmpty() ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Technical Review Approval</span>
                    </div>

                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $curriculumVitae->isNotEmpty() ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Curriculum Vitae of Researcher/s</span>
                    </div>

                    {{-- Optional: If you want to list the names from the CVs like we did for ICF --}}
                    @foreach($curriculumVitae as $cv)
                        <div style="margin-left: 25px; border-bottom: 1px solid black; margin-bottom: 5px; width: 85%; height: 16px; font-size: 12px; padding-left: 5px;">
                            {{ $cv->description }}
                        </div>
                    @endforeach

                    <div class="checkbox-wrapper" style="margin-top: 10px;">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $icfLanguages->isNotEmpty() ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Informed Consent Form</span>
                    </div>

                    @if($icfLanguages->isNotEmpty())
                        @foreach($icfLanguages as $lang)
                            <div style="margin-left: 25px; border-bottom: 1px solid black; margin-bottom: 5px; width: 85%; min-height: 16px; font-size: 12px; padding-left: 5px; display: flex; align-items: center;">
                                <span style="font-style: italic; color: #374151;">Language:</span>&nbsp;{{ $lang }}
                            </div>
                        @endforeach
                    @else
                        {{-- Fallback empty lines if no ICF is provided, to maintain form structure --}}
                        <div style="margin-left: 25px; border-bottom: 1px solid black; margin-bottom: 5px; width: 85%; height: 16px;"></div>
                    @endif
                </td>

                <td style="padding: 10px; border-left: none; border-top: none; vertical-align: top;">
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ !empty($questionnaire) ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Questionnaire (if applicable)</span>
                    </div>
                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ !empty($dataCollection) ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Data Collection Forms (if applicable)</span>
                    </div>
                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ !empty($productBrochure) ? '✔' : '' }}
                            </span>
                            <span class="cb-label">Product Brochure (if applicable)</span>
                        </div>

                        <div class="checkbox-wrapper">
                            <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                                {{ $philippineFda->isNotEmpty() ? '✔' : '' }}
                            </span>
                            <span class="cb-label">Philippine FDA Marketing Authorization<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;or Import License (if applicable)</span>
                        </div>

                    <div class="checkbox-wrapper" style="margin-top: 10px;">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $specialPops->isNotEmpty() ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Permit/s for special populations (please specify)</span>
                    </div>

                    @foreach($specialPops as $doc)
                        <div style="margin-left: 25px; border-bottom: 1px solid black; margin-bottom: 5px; width: 85%; height: 16px; font-size: 12px; padding-left: 5px;">
                            {{ $doc->description }}
                        </div>
                    @endforeach

                    @if($specialPops->isEmpty())
                        <div style="margin-left: 25px; border-bottom: 1px solid black; margin-bottom: 5px; width: 85%; height: 16px;"></div>
                    @endif


                    <div class="checkbox-wrapper" style="margin-top: 10px;">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $others->isNotEmpty() ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Others (please specify)</span>
                    </div>

                    @foreach($others as $doc)
                        <div style="margin-left: 25px; border-bottom: 1px solid black; margin-bottom: 5px; width: 85%; height: 16px; font-size: 12px; padding-left: 5px;">
                            {{ $doc->description }}
                        </div>
                    @endforeach

                    @if($others->isEmpty())
                        <div style="margin-left: 25px; border-bottom: 1px solid black; margin-bottom: 5px; width: 85%; height: 16px;"></div>
                        <div style="margin-left: 25px; border-bottom: 1px solid black; margin-bottom: 5px; width: 85%; height: 20px;"></div>
                    @endif
                </td>
            </tr>
        </table>

        <table class="connected-table">
            <tr>
                <td style="padding: 15px;">
                    <div style="margin-bottom: 40px;">Accomplish</div>
                    <div class="center" style="width: 50%; margin: 50px auto; text-align: center; font-family: 'Times New Roman', serif;">
                        <div style="margin-bottom: 20px;">
                            <div style="height: 60px; display: flex; align-items: flex-end; justify-content: center;">
                                @if($signatureBase64)
                                    <img src="{{ $signatureBase64 }}" alt="E-Signature" style="max-width: 40%; max-height: 80px; margin-bottom: -20px;">
                                @else
                                    <span style="font-style: italic; font-size: 12px; color: #9ca3af; margin-bottom: 5px;">(No signature provided)</span>
                                @endif
                            </div>

                            <div style="font-size: 14px; text-transform: uppercase;">
                                {{ ucwords($application->name_of_researcher ?? 'N/A') }}
                            </div>

                            <div style="border-top: 1px solid black; width: 100%; margin: 5px auto 5px auto;"></div>

                            <div style="font-size: 14px;">Signature over Printed Name</div>
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        Date submitted:
                        <span style="border-bottom: 1px solid black; padding: 0 10px; display: inline-block; min-width: 150px;">
                            {{ $application->created_at ? $application->created_at->format('F j, Y') : date('F j, Y') }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="center small-text" style="border: 1px solid black; border-bottom: 1px solid black; padding: 6px; margin-top: -1px; background: white; font-weight: normal;">
            --------------------- To be filled by the BERC secretariat ---------------------
        </div>

        <table class="connected-table">
            <colgroup>
                <col style="width: 25%;">
                <col style="width: 30%;">
                <col style="width: 45%;">
            </colgroup>
            <tr>
                <td>Completeness of Document</td>
                <td>
                    @php
                        // Check against the specific secretariat logging statuses
                        $isComplete = $checkingLog && in_array($checkingLog->status, ['documents_complete', 'documents_checking']);
                        $isIncomplete = $checkingLog && $checkingLog->status === 'incomplete_documents';
                    @endphp

                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $isComplete ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Complete</span>
                    </div>

                    <div class="checkbox-wrapper">
                        <span class="box" style="display: flex; justify-content: center; align-items: center; font-weight: bold;">
                            {{ $isIncomplete ? '✔' : '' }}
                        </span>
                        <span class="cb-label">Incomplete</span>
                    </div>
                </td>
                <td rowspan="4"></td>
            </tr>
            <tr>
                <td>Remarks</td>
                <td>
                    {{-- Display the synthesized comment from the checking log --}}
                    {{ $checkingComment ?? '' }}
                </td>
            </tr>
            <tr>
                <td>Date Received</td>
                <td>
                    <span style="display: inline-block; min-width: 150px;">
                        {{-- Use the checking log date if it exists, otherwise fallback to application creation --}}
                        {{ $checkingLog ? $checkingLog->created_at->format('F j, Y') : $application->created_at->format('F j, Y') }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Received by</td>
                <td>
                    {{-- Display the Staff Name (User) who did the document checking --}}
                    {{ $checkingUser ? $checkingUser->name : '' }}
                </td>
            </tr>
        </table>

        <div class="footer-tracking">
            Tracking No. <span style="border-bottom: 1px solid black; padding: 0 10px; display: inline-block; min-width: 50px; text-align: center;">{{ $application->id }}</span>
        </div>
    </div>

</body>

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
</html>
