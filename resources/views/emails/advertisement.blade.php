<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0;background:#f3f6fb;color:#111c2d;font-family:Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f6fb;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;overflow:hidden;border-radius:16px;background:#ffffff;box-shadow:0 8px 30px rgba(0,52,111,.10);">
                    <tr>
                        <td style="background:#00346f;padding:22px 28px;color:#ffffff;font-size:22px;font-weight:bold;">DVSE.UK</td>
                    </tr>
                    @if($imageUrl)
                        <tr>
                            <td><img src="{{ $imageUrl }}" alt="" width="600" style="display:block;width:100%;max-height:320px;object-fit:cover;"></td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding:32px 28px;">
                            <p style="margin:0 0 18px;color:#505f76;font-size:15px;">Hello {{ $recipientName }},</p>
                            <h1 style="margin:0 0 16px;color:#00346f;font-size:26px;line-height:1.25;">{{ $headline }}</h1>
                            <div style="color:#334155;font-size:16px;line-height:1.7;">{!! nl2br(e($messageBody)) !!}</div>
                            @if($linkUrl && $buttonLabel)
                                <p style="margin:28px 0 0;">
                                    <a href="{{ $linkUrl }}" style="display:inline-block;border-radius:8px;background:#004a99;padding:13px 22px;color:#ffffff;font-weight:bold;text-decoration:none;">{{ $buttonLabel }}</a>
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top:1px solid #e2e8f0;padding:20px 28px;color:#64748b;font-size:12px;">Sent by DVSE.UK</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
