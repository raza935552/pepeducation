<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:600px;margin:0 auto;padding:24px;">
        <div style="background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #eee;">
            <div style="background:#111827;padding:20px 24px;">
                <p style="margin:0;color:#fff;font-size:18px;font-weight:700;">Professor Peptides</p>
                <p style="margin:4px 0 0;color:#cbd5e1;font-size:13px;">Your chat transcript</p>
            </div>
            <div style="padding:24px;">
                <p style="margin:0 0 16px;font-size:15px;color:#333;">
                    Hi {{ $conversation->displayName() ?: 'there' }}, here's a copy of our conversation for your records.
                </p>
                <div style="border:1px solid #eef0f2;border-radius:10px;overflow:hidden;">
                    @foreach($messages as $m)
                        <div style="padding:12px 16px;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}background:{{ $m['is_visitor'] ? '#ffffff' : '#f8fafc' }};">
                            <p style="margin:0 0 4px;font-size:12px;font-weight:600;color:{{ $m['is_visitor'] ? '#6b7280' : '#2563eb' }};">
                                {{ $m['who'] }} <span style="font-weight:400;color:#9ca3af;">· {{ $m['time'] }}</span>
                            </p>
                            <p style="margin:0;font-size:14px;color:#333;line-height:1.5;">{!! nl2br(e($m['body'])) !!}</p>
                        </div>
                    @endforeach
                </div>
                <p style="margin:20px 0 0;font-size:13px;color:#6b7280;">
                    Need anything else? Just reply to this email or start a new chat anytime at
                    <a href="{{ url('/') }}" style="color:#2563eb;">professorpeptides.co</a>.
                </p>
                <p style="margin:14px 0 0;font-size:12px;color:#9ca3af;">For research &amp; educational use only.</p>
            </div>
        </div>
    </div>
</body>
</html>
