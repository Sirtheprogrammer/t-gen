<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Key Changed Alert</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 30px 0; }
        .wrapper { max-width: 580px; margin: 0 auto; }
        .card { background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #16a34a, #22c55e); padding: 36px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
        .header p { color: rgba(255,255,255,0.9); margin: 8px 0 0; font-size: 14px; }
        .header .icon-wrap { display: inline-block; width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; margin-bottom: 12px; }
        .body { padding: 36px 40px; }
        .alert-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px 20px; margin-bottom: 28px; }
        .alert-box p { margin: 0; color: #166534; font-size: 14px; }
        .alert-box .alert-icon { vertical-align: middle; margin-right: 8px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table tr td { padding: 12px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .info-table tr:last-child td { border-bottom: none; }
        .info-table .label { color: #64748b; font-weight: 500; width: 140px; }
        .info-table .value { color: #0f172a; font-weight: 600; }
        .footer { text-align: center; padding: 24px 40px; background: #f8fafc; border-top: 1px solid #e2e8f0; }
        .footer p { margin: 0; color: #94a3b8; font-size: 12px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <div class="icon-wrap">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 10px;">
                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                </svg>
            </div>
            <h1>API Key Updated</h1>
            <p>The API key for a payment gateway was recently changed</p>
        </div>
        <div class="body">
            <div class="alert-box">
                <p><svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>If you did not make this change, please log in immediately and review your payment gateway settings from the admin <strong>Dashboard</strong>.</p>
            </div>
            <table class="info-table">
                <tr>
                    <td class="label">Gateway</td>
                    <td class="value">{{ $gateway->display_name }}</td>
                </tr>
                <tr>
                    <td class="label">Time</td>
                    <td class="value">{{ $timeChanged }}</td>
                </tr>
                <tr>
                    <td class="label">IP Address</td>
                    <td class="value">{{ $ipAddress }}</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <p>This is an automated security notification from {{ config('app.name') }}.</p>
        </div>
    </div>
</div>
</body>
</html>
