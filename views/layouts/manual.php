<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario — Crédinor</title>
    <link rel="icon" type="image/png" href="<?= asset('img/logo.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --brand-light: #eff6ff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --warn-bg: #fff7ed;
            --warn-border: #f97316;
            --tip-bg: #eff6ff;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; font-size: 14px; color: var(--text); background: #f8fafc; line-height: 1.7; }

        /* ── TOP BAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            background: white; border-bottom: 1px solid var(--border);
            padding: 12px 32px; display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .topbar-brand { font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: var(--brand); }
        .topbar-brand span { color: var(--text); }
        .btn-pdf {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--brand); color: white; font-weight: 600; font-size: 13px;
            padding: 8px 18px; border-radius: 8px; border: none; cursor: pointer;
            text-decoration: none; transition: background .15s;
        }
        .btn-pdf:hover { background: var(--brand-dark); }
        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            color: var(--muted); font-size: 13px; font-weight: 500;
            text-decoration: none; margin-right: 16px;
            padding: 8px 12px; border-radius: 8px; transition: background .15s;
        }
        .btn-back:hover { background: #f1f5f9; color: var(--text); }

        /* ── LAYOUT ── */
        .wrapper { max-width: 860px; margin: 40px auto 80px; padding: 0 24px; }

        /* ── PORTADA ── */
        .cover {
            background: white; border-radius: 16px; border: 1px solid var(--border);
            padding: 56px 48px; text-align: center; margin-bottom: 40px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        .cover-logo { font-family: 'Outfit', sans-serif; font-size: 42px; font-weight: 900; color: var(--brand); }
        .cover-sub  { color: var(--muted); margin-top: 4px; font-size: 15px; }
        .cover-title { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 800; color: var(--text); margin-top: 32px; }
        .cover-badge { display: inline-block; background: var(--brand-light); color: var(--brand); font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 999px; margin-top: 10px; }

        /* ── SECCIÓN ── */
        .section { background: white; border-radius: 14px; border: 1px solid var(--border); padding: 32px 36px; margin-bottom: 28px; box-shadow: 0 1px 6px rgba(0,0,0,.04); }

        /* ── ROLE HEADER ── */
        .role-header {
            background: linear-gradient(135deg, var(--brand-dark) 0%, #3b82f6 100%);
            color: white; padding: 14px 24px; border-radius: 12px;
            font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 800;
            letter-spacing: .3px; margin: 36px 0 4px; display: flex; align-items: center; gap: 10px;
        }

        /* ── HEADINGS ── */
        h2 { font-family: 'Outfit', sans-serif; font-size: 17px; font-weight: 700; color: var(--brand-dark); margin: 24px 0 10px; border-bottom: 2px solid #bfdbfe; padding-bottom: 6px; }
        h3 { font-size: 13px; font-weight: 700; color: var(--text); margin: 18px 0 8px; }
        p  { margin-bottom: 10px; color: #334155; }
        ul, ol { margin: 6px 0 12px 22px; }
        li { margin-bottom: 5px; color: #334155; }
        strong { color: var(--text); }

        /* ── TABLE ── */
        table { width: 100%; border-collapse: collapse; margin: 12px 0 18px; font-size: 13px; }
        thead th { background: var(--brand); color: white; padding: 8px 12px; text-align: left; font-weight: 600; }
        thead th:first-child { border-radius: 8px 0 0 0; }
        thead th:last-child  { border-radius: 0 8px 0 0; }
        td { padding: 8px 12px; border-bottom: 1px solid var(--border); }
        tr:nth-child(even) td { background: #f8fafc; }
        tr:last-child td { border-bottom: none; }

        /* ── CALLOUTS ── */
        .tip  { background: var(--tip-bg); border-left: 4px solid var(--brand); padding: 10px 14px; border-radius: 0 8px 8px 0; margin: 12px 0; font-size: 13px; color: #1e40af; }
        .warn { background: var(--warn-bg); border-left: 4px solid var(--warn-border); padding: 10px 14px; border-radius: 0 8px 8px 0; margin: 12px 0; font-size: 13px; color: #9a3412; }

        /* ── FLUJO ── */
        .flow { display: flex; flex-direction: column; gap: 0; margin: 12px 0; }
        .flow-step { display: flex; align-items: flex-start; gap: 14px; }
        .flow-step .num { min-width: 28px; height: 28px; background: var(--brand); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; margin-top: 1px; }
        .flow-step .text { padding: 4px 0 0; color: #334155; font-size: 13px; }
        .flow-arrow { margin-left: 14px; color: #93c5fd; font-size: 18px; line-height: 1; }

        /* ── FAQ ── */
        .faq-item { border: 1px solid var(--border); border-radius: 10px; margin-bottom: 10px; overflow: hidden; }
        .faq-q { padding: 12px 16px; font-weight: 600; color: var(--brand-dark); font-size: 13px; background: var(--brand-light); }
        .faq-a { padding: 10px 16px; font-size: 13px; color: #475569; }

        /* ── PRINT ── */
        @media print {
            body { background: white; font-size: 11px; }
            .topbar { display: none; }
            .wrapper { margin: 0; padding: 16px; max-width: 100%; }
            .cover { box-shadow: none; border: none; padding: 40px 24px; }
            .section { box-shadow: none; border: 1px solid #e2e8f0; page-break-inside: avoid; }
            .role-header { page-break-before: always; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .btn-pdf, .btn-back { display: none; }
            thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <div style="display:flex;align-items:center;">
        <a href="<?= url('dashboard') ?>" class="btn-back">← Volver</a>
        <div class="topbar-brand">Crédinor <span style="font-weight:400;font-size:14px;color:var(--muted)">— Manual de Usuario</span></div>
    </div>
    <button class="btn-pdf" onclick="window.print()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Imprimir / Guardar PDF
    </button>
</div>

<div class="wrapper">
    <?= $content ?>
</div>

</body>
</html>
