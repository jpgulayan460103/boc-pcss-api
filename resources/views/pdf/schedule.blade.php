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
            margin-top: 0.25in;
            margin-left: 0.5in;
            margin-right: 0.5in;
            margin-bottom: 0.5in;
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

<!-- <htmlpageheader name="page-header">
    <div>
        <img style="margin-top: 20pt;" src="{{ public_path('images/pdf-header-1.png') }}" alt="">
    </div>
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <div style="padding-bottom: 10pt;">
        <img src="{{ public_path('images/pdf-footer-1.png') }}" alt="">
    </div>
</htmlpagefooter> -->
    <p style="text-align: center;">
        <b>
            <span id="heading">Working Schedule</span><br>
            <span id="sub-heading">{{ $schedule->working_start_date->format("F d, Y") }} - {{ $schedule->working_end_date->format("F d, Y") }}</span>
        </b>
    </p>
    <table id="schedule-table">
        <thead>
            <tr>
                <th style="text-align: center;">Schedule Date and Time</th>
                <th style="text-align: center;">Full Name</th>
                <th style="text-align: center;">Position</th>
                <th style="text-align: center;">Assigned Office</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $scheduleKey => $workingDates)
                @foreach($workingDates as $workingDateKey => $shifts)
                    <tr>
                        <td style="text-align: center;">
                            {{ $shifts[0]['working_date'] }}<br>
                            {{ $shifts[0]['shift'] }}
                        </td>
                        <td style="text-align: center;">
                            @foreach($shifts as $shiftKey => $employee)
                            <span>{{ $employee['full_name'] }}<br></span>
                            @endForeach
                        </td>
                        <td style="text-align: center;">
                            @foreach($shifts as $shiftKey => $employee)
                            <span>{{ $employee['position'] }}<br></span>
                            @endForeach
                        </td>
                        <td style="text-align: center;">{{ $shifts[0]['assigned_office'] }}</td>
                    </tr>
                @endForeach
            @endForeach
        </tbody>
    </table>
</body>
</html>