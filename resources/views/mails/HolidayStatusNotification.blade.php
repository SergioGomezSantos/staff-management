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

        .header.approved {
            border-color: #10b981;
        }

        .header.rejected {
            border-color: #ef4444;
        }

        .details {
            margin: 15px 0;
        }

        .detail-item {
            margin-bottom: 8px;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }

        .status {
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .status.approved {
            background-color: #d1f2eb;
            color: #0d5345;
        }

        .status.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="header {{ $data['status'] === 'approved' ? 'approved' : 'rejected' }}">
        <h2>
            @if ($data['status'] === 'approved')
                Your Holiday Request Has Been Approved
            @else
                Your Holiday Request Has Been Declined
            @endif
        </h2>
    </div>

    <div class="details">
        <div class="detail-item">
            <strong>Status:</strong>
            <span class="status {{ $data['status'] === 'approved' ? 'approved' : 'rejected' }}">{{ strtoupper($data['status']) }}</span>
        </div>
        <div class="detail-item">
            <strong>Dates:</strong>
            {{ \Carbon\Carbon::parse($data['start_date'])->format('M j, Y') }}
            @isset($data['end_date'])
                @if ($data['end_date'] != $data['start_date'])
                    - {{ \Carbon\Carbon::parse($data['end_date'])->format('M j, Y') }}
                @endif
            @endisset
        </div>

        @if ($data['rejection_reason'])
            <hr>
            <div class="detail-item">
                <strong>Comments:</strong>
                {{ $data['rejection_reason'] }}
            </div>
        @endif
    </div>

    <div class="footer">
        <p>This is an automated notification. Please do not reply directly to this email.</p>
    </div>
</body>

</html>
