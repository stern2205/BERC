<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logbook of Outgoing Communications - {{ $protocol_code }}</title>
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
      }

      /* 13in x 8.5in Landscape (Long Bond Paper) */
      @page {
        size: 13in 8.5in landscape;
        margin: 0.4in;
      }

      .page-container {
        width: 12.2in;
        margin: 20px auto;
        background-color: white;
        padding: 0;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }

      @media print {
        body { background-color: white; }
        .page-container {
          margin: 0;
          box-shadow: none;
          width: 100%;
        }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
      }

      table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        border: 1.5px solid black;
      }

      th, td {
        border: 1px solid black;
        padding: 6px;
        word-wrap: break-word;
        font-size: 10pt;
      }

      .logo-img {
        width: 65px;
        height: auto;
        display: block;
        margin: 0 auto;
      }

      .center { text-align: center; }
      .bold { font-weight: bold; }
      .v-middle { vertical-align: middle; }
      .h-center { text-align: center; }

      .log-table th {
        background-color: #f2f2f2 !important;
        font-weight: bold;
        text-align: center;
        height: 60px;
      }

      .data-row td {
        height: 50px;
        vertical-align: middle;
      }

      .name-print {
        display: block;
        font-size: 8.5pt;
        margin-top: 8px;
        border-top: 1px dotted #999;
        padding-top: 2px;
      }

      .footer-info {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
        font-style: italic;
        font-size: 10pt;
      }

      /* Year line */
      .year-line {
        text-align: center;
        margin: 18px 0 20px 0;
        font-size: 11pt;
      }

      .year-line span {
        border-bottom: 1px solid black;
        display: inline-block;
        min-width: 120px;
        font-weight: bold;
      }

      /* Log table specific row heights */
      .header-row td {
        font-weight: bold;
        background-color: #f9f9f9;
        text-align: center;
        height: auto;
      }

      .data-row td {
        height: 45px;
        font-size: 10.5pt;
      }

      .row-number {
        text-align: center;
        width: 40px;
      }

      .nature-cell {
        font-weight: 500;
        text-transform: capitalize;
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
          <td class="center">
            <img src="{{ asset('logo/bsu_logo.png') }}" alt="Logo" class="logo-img" />
          </td>
          <td class="header-text">Reference No.: BatStateU-FO-LOGBOOK-002</td>
          <td class="header-text">Effectivity Date:</td>
          <td class="header-text">Revision No.: 00</td>
        </tr>
      </table>

      <div class="center bold" style="border: 1px solid black; border-top: none; padding: 12px; font-size: 12pt; text-transform: uppercase; margin-top: -1px;">
        Logbook of Outgoing Communications
      </div>

      <div class="year-line">
            YEAR:
            <span>
                {{ count($logbookEntries) > 0 ? explode(',', $logbookEntries->first()['date'])[1] ?? date('Y') : date('Y') }}
            </span>
        </div>

      <table class="log-table">
        <colgroup>
          <col style="width: 4%" />   <col style="width: 15%" />  <col style="width: 24%" />  <col style="width: 15%" />  <col style="width: 15%" />  <col style="width: 13.5%" /> <col style="width: 13.5%" /> </colgroup>

        <thead>
          <tr class="header-row">
            <td>&nbsp;</td>
            <td>Date</td>
            <td>
              Nature of Document<br />
              <span style="font-weight: normal; font-size: 9pt">(Decision, Invitation, etc.)</span>
            </td>
            <td>Signatory</td>
            <td>Addressee</td>
            <td>
              Received by<br />
              <span style="font-weight: normal; font-size: 8pt">(Name & Signature)</span>
            </td>
            <td>
              Delivered by<br />
              <span style="font-weight: normal; font-size: 8pt">(Name & Signature)</span>
            </td>
          </tr>
        </thead>

        <tbody>
            @forelse ($logbookEntries as $index => $entry)
                <tr class="data-row" style="height: 75px;"> <td class="row-number" style="font-size: 11px; color: #666;">{{ $index + 1 }}.</td>
                    <td class="v-middle h-center" style="font-size: 10px; line-height: 1.2;">{{ $entry['date'] }}</td>
                    <td class="nature-cell" style="font-size: 11px; padding: 0 5px;">{{ $entry['nature'] }}</td>

                    <td class="h-center" style="position: relative; vertical-align: bottom; padding-bottom: 8px;">
                        @if($entry['has_signatory_sig'])
                            <img src="/signature/user/{{ $entry['signatory_id'] }}"
                                style="position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%) rotate(-2deg); height: 45px; width: auto; z-index: 1; mix-blend-mode: multiply; pointer-events: none; opacity: 0.9;">
                        @endif
                        <span style="position: relative; z-index: 2; font-size: 10px; font-weight: bold; text-transform: uppercase; border-top: 0.5px solid #ddd; display: block; margin: 0 4px;">
                            {{ $entry['signatory'] }}
                        </span>
                    </td>

                    <td class="h-center" style="font-size: 11px;">{{ $entry['addressee'] }}</td>

                    <td class="h-center" style="position: relative; vertical-align: bottom; padding-bottom: 8px;">
                        @if($entry['has_received_sig'])
                            <img src="/signature/user/{{ $entry['received_by_id'] }}"
                                style="position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%) rotate(1deg); height: 45px; width: auto; z-index: 1; mix-blend-mode: multiply; pointer-events: none; opacity: 0.9;">
                        @endif
                        <span class="name-print" style="position: relative; z-index: 2; font-size: 10px; font-weight: bold; text-transform: uppercase; border-top: 0.5px solid #ddd; display: block; margin: 0 4px;">
                            {{ $entry['received_by'] }}
                        </span>
                    </td>

                    <td class="h-center" style="position: relative; vertical-align: bottom; padding-bottom: 8px;">
                        @if($entry['has_delivered_sig'])
                            <img src="/signature/user/{{ $entry['delivered_id'] }}"
                                style="position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%) rotate(-1deg); height: 45px; width: auto; z-index: 1; mix-blend-mode: multiply; pointer-events: none; opacity: 0.9;">
                        @endif
                        <span class="name-print" style="position: relative; z-index: 2; font-size: 10px; font-weight: bold; text-transform: uppercase; border-top: 0.5px solid #ddd; display: block; margin: 0 4px;">
                            {{ $entry['delivered_by'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr class="data-row" style="height: 75px;">
                    <td colspan="7" class="center italic" style="color: #999; font-size: 12px;">
                        No communications have been logged for this protocol.
                    </td>
                </tr>
            @endforelse

            {{-- Fill remaining rows for aesthetic consistency --}}
            @php $remaining = 8 - count($logbookEntries); @endphp
            @if($remaining > 0)
                @for ($i = 1; $i <= $remaining; $i++)
                    <tr class="data-row" style="height: 75px;">
                        <td class="row-number" style="color: #eee;">{{ count($logbookEntries) + $i }}.</td>
                        <td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                @endfor
            @endif
        </tbody>
      </table>

      <div class="footer-info">
        Tracking No.
        <span style="display: inline-block; min-width: 180px; border-bottom: 1px solid black; text-align: center; font-weight: bold; font-style: normal;">
            {{ $protocol_code }}
        </span>
      </div>
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
