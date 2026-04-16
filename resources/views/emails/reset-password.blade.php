<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de senha</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f3f4f6; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:640px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="background:linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%); padding:32px 24px; text-align:center; color:#ffffff;">
                            <img
                                src="{{ asset('logo-prefeitura.png') }}"
                                alt="Logo Prefeitura"
                                style="width:96px; height:96px; object-fit:contain; background:#ffffff; border-radius:12px; padding:8px; margin-bottom:16px;"
                                onerror="this.style.display='none';"
                            >
                            <h1 style="margin:0; font-size:24px; line-height:1.2; font-weight:700;">
                                {{ config('app.name', 'Prefeitura') }}
                            </h1>
                            <p style="margin:8px 0 0; font-size:14px; opacity:0.92;">
                                Sistema de Gestão de Frota
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 24px;">
                            <h2 style="margin:0 0 12px; font-size:20px; color:#111827;">
                                Redefinição de senha
                            </h2>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.7; color:#374151;">
                                Olá, {{ $user->name ?? 'usuário' }}!
                            </p>

                            <p style="margin:0 0 18px; font-size:15px; line-height:1.7; color:#374151;">
                                Recebemos uma solicitação para redefinir a senha da sua conta.
                                Para criar uma nova senha, clique no botão abaixo:
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:28px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}"
                                           style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; font-size:15px; font-weight:700; padding:14px 22px; border-radius:10px;">
                                            Redefinir senha
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 10px; font-size:14px; line-height:1.7; color:#4b5563;">
                                Este link expira em 60 minutos.
                            </p>

                            <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#4b5563;">
                                Se você não solicitou essa alteração, ignore este e-mail. Sua senha permanecerá a mesma.
                            </p>

                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">

                            <p style="margin:0; font-size:12px; line-height:1.6; color:#6b7280;">
                                Caso o botão não funcione, copie e cole este link no navegador:
                            </p>

                            <p style="margin:8px 0 0; font-size:12px; line-height:1.6; word-break:break-all;">
                                <a href="{{ $url }}" style="color:#2563eb; text-decoration:underline;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 24px 28px; text-align:center; font-size:12px; color:#6b7280;">
                            Prefeitura Municipal de Cruzeiro do Oeste<br>
                            © {{ date('Y') }} Todos os direitos reservados.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>