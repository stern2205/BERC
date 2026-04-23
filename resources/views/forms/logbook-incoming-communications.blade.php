<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logbook of Incoming Communications - {{ $protocol_code }}</title>
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
        margin-top: 10px;
        display: flex;
        justify-content: space-between;
        font-style: italic;
        font-size: 10pt;
      }
    </style>
  </head>
  <body>
    <div class="page-container">
      <table class="log-table">
        <thead>
          <tr>
            <td colspan="7" style="padding: 0; border: none;">
                <table style="width: 100%; border-collapse: collapse; border: none; border-bottom: 1.5px solid black;">
                    <tr>
                        <td style="width: 15%; border: none;" class="center v-middle">
                            <img src="{{ asset('logo/bsu_logo.png') }}" alt="Logo" class="logo-img">
                        </td>
                        <td style="width: 35%; border: none; border-left: 1px solid black;" class="v-middle">
                            Reference No.: BatStateU-FO-LOGBOOK-001
                        </td>
                        <td style="width: 30%; border: none; border-left: 1px solid black;" class="v-middle">
                            Effectivity Date: {{ now()->format('M d, Y') }}
                        </td>
                        <td style="width: 20%; border: none; border-left: 1px solid black;" class="v-middle">
                            Revision No.: 00
                        </td>
                    </tr>
                </table>
            </td>
          </tr>
          <tr>
            <td colspan="7" class="center bold" style="padding: 10px; font-size: 12pt; text-transform: uppercase;">
                Logbook of Incoming Communications
            </td>
          </tr>
          <tr>
            <td colspan="7" class="center" style="border-top: none; padding-bottom: 10px;">
                YEAR: <span style="border-bottom: 1px solid black; min-width: 100px; display: inline-block;">{{ now()->year }}</span>
            </td>
          </tr>
          <tr style="background-color: #f2f2f2;">
            <th style="width: 4%">#</th>
            <th style="width: 13%">Date</th>
            <th style="width: 23%">Nature of Document</th>
            <th style="width: 15%">Signatory</th>
            <th style="width: 15%">Addressee</th>
            <th style="width: 15%">Received by<br><small>(Name/Signature)</small></th>
            <th style="width: 15%">Delivered by<br><small>(Name/Signature)</small></th>
          </tr>
        </thead>

        <tbody>
            @php $rowCount = 0; @endphp
            @foreach($logbookEntries as $index => $entry)
                @php $rowCount++; @endphp
                <tr class="data-row" style="height: 70px;"> <td class="h-center" style="color: #666; font-size: 11px;">{{ $index + 1 }}.</td>
                    <td class="h-center" style="font-size: 11px; line-height: 1.2;">{{ $entry['date'] }}</td>
                    <td style="font-size: 12px; padding: 0 8px;">{{ $entry['nature'] }}</td>

                    <td class="h-center" style="position: relative; vertical-align: bottom; padding-bottom: 10px;">
                        @if($entry['has_sender_sig'])
                            <img src="/signature/user/{{ $entry['signatory_id'] }}"
                                style="position: absolute;
                                    bottom: 15px; /* Pulls it down to touch the name */
                                    left: 50%;
                                    transform: translateX(-50%) rotate(-2deg); /* Subtle tilt for realism */
                                    height: 50px;
                                    width: auto;
                                    z-index: 1;
                                    mix-blend-mode: multiply;
                                    pointer-events: none;
                                    opacity: 0.85;">
                        @endif
                        <span style="position: relative; z-index: 2; font-size: 11px; font-weight: bold; text-transform: uppercase; border-top: 0.5px solid #eee; display: block; margin: 0 5px; padding-top: 2px;">
                            {{ $entry['signatory'] }}
                        </span>
                    </td>

                    <td class="h-center" style="font-size: 12px;">{{ $entry['addressee'] }}</td>

                    <td class="h-center" style="position: relative; vertical-align: bottom; padding-bottom: 10px;">
                        @if($entry['has_received_sig'])
                            <img src="/signature/user/{{ $entry['received_by_id'] }}"
                                style="position: absolute;
                                    bottom: 15px;
                                    left: 50%;
                                    transform: translateX(-50%) rotate(1deg);
                                    height: 50px;
                                    width: auto;
                                    z-index: 1;
                                    mix-blend-mode: multiply;
                                    pointer-events: none;
                                    opacity: 0.85;">
                        @endif
                        <span class="name-print" style="position: relative; z-index: 2; font-size: 11px; font-weight: bold; text-transform: uppercase; border-top: 0.5px solid #eee; display: block; margin: 0 5px; padding-top: 2px;">
                            {{ $entry['received_by'] }}
                        </span>
                    </td>

                    <td class="h-center" style="position: relative; vertical-align: bottom; padding-bottom: 10px;">
                        @if($entry['has_delivered_sig'])
                            <img src="/signature/user/{{ $entry['delivered_id'] }}"
                                style="position: absolute;
                                    bottom: 15px;
                                    left: 50%;
                                    transform: translateX(-50%) rotate(-1deg); /* Slight tilt for variety */
                                    height: 50px;
                                    width: auto;
                                    z-index: 1;
                                    mix-blend-mode: multiply;
                                    pointer-events: none;
                                    opacity: 0.85;">
                        @endif

                        <span class="name-print" style="position: relative; z-index: 2; font-size: 11px; font-weight: bold; text-transform: uppercase; border-top: 0.5px solid #eee; display: block; margin: 0 5px; padding-top: 2px;">
                            {{ $entry['delivered_by'] }}
                        </span>
                    </td>
                </tr>
            @endforeach

            {{-- Empty rows for padding --}}
            @for($i = $rowCount + 1; $i <= max(12, $rowCount); $i++)
                <tr class="data-row" style="height: 70px;">
                    <td class="h-center" style="color: #ccc;">{{ $i }}.</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endfor
        </tbody>
      </table>

      <div class="footer-info">
        <span>Protocol Code: <strong>{{ $protocol_code }}</strong></span>
        <span>Tracking No. ________________________</span>
      </div>
    </div>

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
  </body>
</html>
