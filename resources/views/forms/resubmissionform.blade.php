<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resubmission Form - {{ $application->protocol_code }}</title>
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
        min-height: 13in;
        background-color: white;
        margin: 20px auto;
        padding: 0.5in;
        position: relative;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
          min-height: 13in;
          padding: 0.5in;
          page-break-after: always;
        }
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
        margin: 0;
        table-layout: fixed;
      }

      td, th {
        border: 1px solid black;
        padding: 5px 6px;
        vertical-align: top;
        word-wrap: break-word;
      }

      .header-table td {
        padding: 2px 5px;
        vertical-align: middle;
        height: auto;
      }

      .v-middle { vertical-align: middle; }
      .h-center { text-align: center; }

      .logo-img {
        width: 65px;
        height: auto;
        display: block;
        margin: 0 auto;
      }

      /* Page Break Prevention for Rows */
      tr.response-row {
          page-break-inside: avoid;
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
            <img src="{{ asset('logo/bsu_logo.png') }}" alt="Logo" class="logo-img" />
          </td>
          <td class="header-text">Reference No.: BatStateU-FO-BERC-010</td>
          <td class="header-text">Effectivity Date:</td>
          <td class="header-text">Revision No.: {{ str_pad($latestRevision->revision_number, 2, '0', STR_PAD_LEFT) }}</td>
        </tr>
      </table>

      <div class="center bold" style="border: 1px solid black; border-top: none; padding: 12px; font-size: 12pt; text-transform: uppercase; margin-top: -1px; margin-bottom: 20px;">
        RESUBMISSION FORM
      </div>

      <table>
        <colgroup>
          <col style="width: 22%" />
          <col style="width: 28%" />
          <col style="width: 18%" />
          <col style="width: 32%" />
        </colgroup>

        <tr>
          <td colspan="4" class="bold" style="background-color: #fff">
            General Information
          </td>
        </tr>

        <tr>
          <td>Title of the Study</td>
          <td colspan="3" style="height: 40px; font-weight: bold;">{{ $application->research_title }}</td>
        </tr>

        <tr>
          <td>Version number/date</td>
          <td colspan="3" style="height: 40px">V{{ $latestRevision->revision_number }} / {{ \Carbon\Carbon::parse($latestRevision->created_at)->format('F d, Y') }}</td>
        </tr>

        <tr>
          <td style="height: 40px">BERC Code (To be<br />Provided by BERC)</td>
          <td class="bold">{{ $application->protocol_code }}</td>
          <td>Study Site</td>
          <td>{{ $application->study_site }}</td>
        </tr>

        <tr>
          <td rowspan="3">Name of Researcher</td>
          <td rowspan="3" class="bold">{{ $application->name_of_researcher }}</td>
          <td rowspan="4" class="v-middle center">Contact<br />Information</td>
          <td style="height: 30px">Tel No: {{ $application->tel_no }}</td>
        </tr>
        <tr>
          <td style="height: 30px">Mobile Number: {{ $application->mobile_no }}</td>
        </tr>
        <tr>
          <td style="height: 30px">Fax No: {{ $application->fax_no }}</td>
        </tr>

        <tr>
          <td style="height: 30px">Co-Researcher (if any)</td>
          <td>{{ is_array($application->co_researchers) ? implode(', ', $application->co_researchers) : $application->co_researchers }}</td>
          <td>Email: {{ $application->email }}</td>
        </tr>

        <tr>
          <td>Institution</td>
          <td colspan="3" style="height: 30px">{{ $application->institution }}</td>
        </tr>

        <tr>
          <td>Address of Institution</td>
          <td colspan="3" style="height: 30px">{{ $application->institution_address }}</td>
        </tr>
      </table>

      <table style="margin-top: 25px">
        <colgroup>
          <col style="width: 34%" />
          <col style="width: 48%" />
          <col style="width: 18%" />
        </colgroup>

        <tr>
          <td class="bold center" style="padding: 10px 6px">BERC Recommendations</td>
          <td class="bold center" style="padding: 10px 6px">Response of Researcher</td>
          <td class="bold center" style="padding: 10px 6px">Section and page<br />number of<br />revisions</td>
        </tr>

        @php
            $currentSectionId = null;
            $sectionTitles = [
                1 => 'Scientific Design',
                2 => 'Conduct of Study',
                3 => 'Ethical Consideration',
                4 => 'Informed Consent'
            ];
        @endphp

        {{-- Loop through the dynamically submitted rows --}}
        @forelse($responses as $response)
            @php
                // Get the first number of the item (e.g., "1.4" becomes 1, "4.12" becomes 4)
                $secId = $response->item ? (int)explode('.', $response->item)[0] : null;
            @endphp

            {{-- Print the Section Header if it's a new section --}}
            @if($secId && $secId !== $currentSectionId)
                @php $currentSectionId = $secId; @endphp
                <tr>
                    <td colspan="3" style="background-color: #e9ecef; font-weight: bold; text-transform: uppercase; padding: 8px 10px; font-size: 10pt;">
                        {{ $sectionTitles[$secId] ?? 'General Revisions' }}
                    </td>
                </tr>
            @endif

            <tr class="response-row">
              <td style="padding: 10px;">
                @if($response->normalized_item)
                    <div style="font-weight: bold; margin-bottom: 4px; text-transform: uppercase; font-size: 9pt;">
                        Item {{ $response->normalized_item }}
                    </div>
                @endif

                <div style="margin-bottom: 8px; font-size: 9pt; text-align: justify;">
                    {{ $response->full_question }}
                </div>

                <div style="text-align: justify;">
                    BERC Comments: {!! nl2br(e($response->berc_recommendation)) !!}
                </div>
              </td>

              <td style="padding: 10px;">
                  <div style="text-align: justify;">
                      {!! nl2br(e($response->researcher_response)) !!}
                  </div>
              </td>

              <td style="padding: 10px; text-align: center; vertical-align: middle;">
                  {{ $response->section_and_page }}
              </td>
            </tr>
        @empty
            <tr>
              <td style="height: 100px; text-align: center; vertical-align: middle;" colspan="3">
                <span class="italic">No specific recommendations found.</span>
              </td>
            </tr>
        @endforelse
      </table>

      <div style="margin-top: 40px; line-height: 2; page-break-inside: avoid;">
        <div>
          Signature of Researcher: <span style="text-decoration: underline; padding: 0 50px; font-style: italic;">e-Signed by {{ $application->name_of_researcher }}</span>
        </div>
        <div>Date: <span style="text-decoration: underline; padding: 0 20px;">{{ \Carbon\Carbon::parse($latestRevision->created_at)->format('M d, Y') }}</span></div>
      </div>

      <div class="footer-tracking">Tracking No. {{ $application->protocol_code }}</div>
    </div>

    {{-- Auto-Print Script (Optional) --}}
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
