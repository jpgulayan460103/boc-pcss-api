<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Port of Clark Schedule</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
            margin-top: 1.25in;
            margin-left: 0.25in;
            margin-right: 0.25in;
            margin-bottom: 0.75in;
        }
        @font-face {
            font-family: arialnarrow;
            src: url("{{ public_path('fonts/ARIALN.TTF') }}");
        }
        body {
            font-family: arialNarrow, sans-serif;
        }
        #schedule-table tr td{
            border: 1px solid black;
        }
        #schedule-table tr th{
            border: 1px solid black;
        }
        #schedule-table {
            border-collapse: collapse;
            border: 1px;
            border-style: dotted;
            width: 100%;
            font-size: 8pt;
        }
        #heading{
            font-size: 16pt;
        }
        #sub-heading{
            font-size: 12pt;
        }
    </style>
</head>
<body>

<htmlpageheader name="page-header">
    <div>
        <img style="margin-top: 20pt;" src="{{ public_path('images/pdf-header-1.png') }}" alt="">
    </div>
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <div style="padding-bottom: 10pt;">
        <!-- <span style="top: 300pt;" class="fill-text">asdasd</span> -->
        <img src="{{ public_path('images/pdf-footer-1.png') }}" alt="">
    </div>
</htmlpagefooter>
    <p style="text-align: center;">
        <b>
            <span id="heading">Working Schedule</span><br>
            <span id="sub-heading">{{ $schedule->working_start_date->format("F d, Y") }} - {{ $schedule->working_end_date->format("F d, Y") }}</span>
        </b>
    </p>
    <table id="schedule-table">
        <thead>
            <tr>
                <th style="text-align: center;">Schedule Date</th>
                <th style="text-align: center;">Type of Duty</th>
                <th style="text-align: center;">Assigned Office</th>
                <th style="text-align: center;">Full Name</th>
                <th style="text-align: center;">Originating Office</th>
                <th style="text-align: center;">Position</th>
                <th style="text-align: center;">Employee Type</th>
                <th style="text-align: center;">Shift</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
            <tr>
                <td style="text-align: center;">{{ $employee['working_date'] }}</td>
                <td style="text-align: center;">{{ $employee['duty_type'] }}</td>
                <td style="text-align: center;">{{ $employee['assigned_office'] }}</td>
                <td style="text-align: center;">{{ $employee['full_name'] }}</td>
                <td style="text-align: center;">{{ $employee['origin_office'] }}</td>
                <td style="text-align: center;">{{ $employee['position'] }}</td>
                <td style="text-align: center;">{{ $employee['is_overtimer'] }}</td>
                <td style="text-align: center;">{{ $employee['shift'] }}</td>
            </tr>
            @endForeach
        </tbody>
    </table>
</body>
</html>