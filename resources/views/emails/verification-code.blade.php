<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode verifikasi email</title>
</head>

<body style="margin:0;padding:0;background:#f4f5f7;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f5f7;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width:520px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#1e56fc;padding:20px 28px;color:#ffffff;">
                            <p style="margin:0;font-size:18px;font-weight:700;">{{ config('app.name', 'instanweb') }}</p>
                            <p style="margin:4px 0 0;font-size:12px;opacity:.85;">Verifikasi keamanan akun</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 12px;font-size:16px;font-weight:700;">Halo, {{ $name }}</p>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#4b5563;">
                                Gunakan kode berikut untuk memverifikasi alamat email Anda. Kode berlaku selama
                                {{ \App\Services\ContactVerificationService::CODE_TTL_MINUTES }} menit.
                            </p>

                            <div
                                style="margin:0 0 20px;padding:16px;text-align:center;background:#f4f7ff;border:1px dashed #b8c9ff;border-radius:12px;">
                                <span
                                    style="display:block;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#5b6b8c;">Kode
                                    verifikasi</span>
                                <span
                                    style="display:block;margin-top:8px;font-size:32px;font-weight:800;letter-spacing:8px;color:#1e56fc;">{{ $code }}</span>
                            </div>

                            <p style="margin:0;font-size:13px;line-height:1.7;color:#6b7280;">
                                Jika Anda tidak merasa melakukan pendaftaran atau perubahan email, abaikan email ini.
                                Jangan bagikan kode kepada siapa pun.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px;background:#f9fafb;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;font-size:11px;color:#9ca3af;">Email otomatis dari
                                {{ config('app.name', 'instanweb') }}. Mohon tidak membalas email ini.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
