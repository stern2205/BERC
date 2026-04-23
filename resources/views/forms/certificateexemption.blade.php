<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Exemption - {{ $certificate->berc_code }}</title>
    <style>
        /* Print & Page Setup for Long/Folio (8.5 x 13) */
        @page {
            size: 8.5in 13in;
            margin: 0; /* Completely remove browser margins, let the .page container handle it */
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            color: #000000;
            line-height: 1.5;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            /* Force exact colors when printing */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* The actual "Paper" wrapper */
        .page {
            background-color: #ffffff;
            width: 8.5in;
            height: 13in;
            padding: 1in; /* 1 inch margins all around the content */
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden; /* Prevents text from expanding the page size */
        }

        /* Document Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .header-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 9pt;
        }

        .header-table .logo-cell {
            width: 70px;
            text-align: center;
        }

        .header-table img {
            width: 55px;
            height: auto;
        }

        .header-table .title-row td {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            padding: 12px;
            text-transform: uppercase;
        }

        /* Form Information Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .info-table td {
            border: 1px solid #000000;
            padding: 8px 10px;
            vertical-align: top;
        }

        .info-table .label-cell {
            width: 35%;
            font-weight: normal;
        }

        .info-table .value-cell {
            font-weight: bold;
        }

        /* Typography & Content Layout */
        p {
            margin: 0 0 15px 0;
            text-align: justify;
        }

        ol {
            margin: 0 0 20px 0;
            padding-left: 25px;
            text-align: justify;
        }

        ol li {
            margin-bottom: 12px;
        }

        /* Signatory Section */
        .signatory-section {
            margin-top: 60px;
        }

        .signatory-name {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .signatory-title {
            margin-top: 0;
        }

        /* Footer */
        .document-footer {
            position: absolute;
            bottom: 1in; /* Aligns exactly with the 1-inch bottom margin */
            right: 1in;  /* Aligns exactly with the 1-inch right margin */
            text-align: right;
            font-style: italic;
            font-size: 10pt;
        }

        /* Print-Specific Adjustments */
        @media print {
            body {
                background-color: transparent;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 1in;
                page-break-after: avoid; /* Prevents blank second pages */
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="page">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img alt="BatStateU Logo" src="{{ asset('logo/bsu_logo.png') }}">
                </td>
                <td>Reference No.: BatStateU-FO-BERC-023</td>
                <td>Effectivity Date:</td>
                <td>Revision No.: 00</td>
            </tr>
            <tr class="title-row">
                <td colspan="4">Certificate of Exemption from Ethics Review</td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td class="label-cell">Date:</td>
                <td class="value-cell">{{ \Carbon\Carbon::parse($certificate->date_issued)->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="label-cell">Name of Principal Investigator:</td>
                <td class="value-cell">{{ $certificate->investigator_name }}</td>
            </tr>
            <tr>
                <td class="label-cell">Title of Study/Protocol:</td>
                <td class="value-cell">{{ $certificate->study_title }}</td>
            </tr>
            <tr>
                <td class="label-cell">BERC Code:</td>
                <td class="value-cell">{{ $certificate->berc_code }}</td>
            </tr>
        </table>

        <main>
            <p>After a preliminary review, the BatStateU TNEU Ethics Review Committee deemed it appropriate that the above protocol be EXEMPTED FROM REVIEW.</p>

            <p>This means that the study may be implemented without undergoing an expedited or full review. Neither will the proponents be required to submit further documents to the committee as long as there is no amendment nor alteration in the protocol that will change the nature of the study nor the level of risk involved. Please note also, that the following responsibilities of the investigator/s are maintained while the study is in progress:</p>

            <ol>
                <li>Continuing compliance with the exemption criteria of the National Ethical Guidelines for Research Involving Human Participants 2022 in the duration of the study;</li>
                <li>Nonetheless, such human participants in case reports/case series/non-health research are entitled to compliance of researchers with universal ethical principles of respect for persons, beneficence, and justice, as well as applicable local regulations, including the Data Privacy Act of 2012 (RA 10173). Thus, it is the responsibility of the author/investigator(s) to ensure satisfactory compliance with the aforementioned principles and all applicable regulations, and to obtain informed consent from the human subjects involved, if personally identifiable information will be used in any way.</li>
                <li>No substantial changes in research design, methodology, and subject population from the protocol submitted for exemption. Modifications that significantly affect previous risk-benefit assessments or qualification for exemption may be submitted as a new protocol for initial review.</li>
            </ol>
        </main>

        <div class="signatory-section" style="position: relative; display: inline-block; text-align: center; margin-top: 80px; min-width: 300px;">

            @if(isset($chairperson) && $chairperson->e_signature)
                <img src="/signature/user/{{ $chairperson->id }}"
                    alt="Chairperson Signature"
                    style="position: absolute;
                            bottom: 50px; /* Adjust this to move the signature up or down */
                            left: 50%;
                            transform: translateX(-50%);
                            max-height: 100px;
                            width: auto;
                            z-index: 10;
                            pointer-events: none;
                            opacity: 0.9; /* Makes it look more like real ink over paper */
                            mix-blend-mode: multiply; /* Removes white background if the image isn't transparent */
                    ">
            @endif

            <div style="position: relative; z-index: 20; border-top: 1px solid #000; padding-top: 5px; display: inline-block; min-width: 250px;">
                <p class="signatory-name" style="font-weight: bold; margin: 0; font-size: 16px; text-transform: uppercase;">
                    {{ $certificate->chairperson_name }}
                </p>
                <p class="signatory-title" style="margin: 0; font-size: 13px; line-height: 1.2;">
                    Chairperson<br>
                    BatStateU TNEU Ethics Review Committee
                </p>
            </div>
        </div>

        <div class="document-footer">
            Tracking No. {{ $certificate->tracking_number ?? '___________________' }}
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
