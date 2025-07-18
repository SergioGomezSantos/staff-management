<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f0f4f8;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .action-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .action-new {
            background-color: #d4edda;
            color: #155724;
        }

        .action-updated {
            background-color: #fff3cd;
            color: #856404;
        }

        .details {
            margin: 15px 0;
        }

        .detail-item {
            margin-bottom: 8px;
        }

        .type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .type-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .type-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .type-gray {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="action-badge {{ isset($data['is_edit']) && $data['is_edit'] ? 'action-updated' : 'action-new' }}">
            {{ isset($data['is_edit']) && $data['is_edit'] ? 'UPDATED' : 'NEW REQUEST' }}
        </div>
        <h2>
            @if (isset($data['is_edit']) && $data['is_edit'])
                Holiday Request Updated - Needs Review
            @else
                A Holiday Request Needs to be Reviewed
            @endif
        </h2>
    </div>

    <div class="details">
        <div class="detail-item"><strong>Employee:</strong> {{ $data['name'] }}</div>

        <div class="detail-item">
            <strong>Type:</strong>
            <span
                class="type-badge 
                @switch($data['type'])
                    @case('vacation') type-info @break
                    @case('sick_leave') type-danger @break
                    @case('personal') type-gray @break
                    @case('other') type-gray @break
                    @default type-gray
                @endswitch
        ">
                @switch($data['type'])
                    @case('vacation')
                        Vacation
                    @break

                    @case('sick_leave')
                        Sick Leave
                    @break

                    @case('personal')
                        Personal
                    @break

                    @case('other')
                        Other
                    @break

                    @default
                        {{ ucfirst($data['type']) }}
                @endswitch
            </span>
        </div>

        <div class="detail-item"><strong>Type:</strong> {{ ucfirst($data['type']) }}</div>
        <div class="detail-item"><strong>Dates:</strong>
            {{ \Carbon\Carbon::parse($data['start_date'])->format('M j, Y') }}
            @if (!empty($data['end_date']))
                - {{ \Carbon\Carbon::parse($data['end_date'])->format('M j, Y') }}
            @endif
        </div>
    </div>

    <p>
        @if (isset($data['is_edit']) && $data['is_edit'])
            {{ $data['name'] }} has <strong>updated</strong> their holiday request.
        @else
            {{ $data['name'] }} has <strong>submitted</strong> a new holiday request.
        @endif
    </p>

    <p>You can review this request in the system.</p>

    <div class="footer">
        <p>This is an automated notification. Please do not reply directly to this email.</p>
    </div>
</body>

</html>
