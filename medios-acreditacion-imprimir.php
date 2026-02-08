<?php
require __DIR__ . '/app/bootstrap.php';

$token = isset($_GET['token']) ? trim((string) $_GET['token']) : '';
if ($token === '') {
    http_response_code(404);
    echo 'Credencial no encontrada.';
    exit;
}

$stmt = db()->prepare(
    'SELECT r.*, e.titulo, e.fecha_inicio, e.fecha_fin
     FROM media_accreditation_requests r
     INNER JOIN events e ON e.id = r.event_id
     WHERE r.qr_token = ?
     LIMIT 1'
);
$stmt->execute([$token]);
$request = $stmt->fetch();

if (!$request) {
    http_response_code(404);
    echo 'Credencial no encontrada.';
    exit;
}

$municipalidad = get_municipalidad();
$qrToken = htmlspecialchars($request['qr_token'] ?? '', ENT_QUOTES, 'UTF-8');
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . rawurlencode($qrToken);
$eventTitle = htmlspecialchars($request['titulo'] ?? 'Evento', ENT_QUOTES, 'UTF-8');
$eventDates = htmlspecialchars(($request['fecha_inicio'] ?? '') . ' al ' . ($request['fecha_fin'] ?? ''), ENT_QUOTES, 'UTF-8');
$municipalidadName = htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8');
$fullName = htmlspecialchars(trim(($request['nombre'] ?? '') . ' ' . ($request['apellidos'] ?? '')), ENT_QUOTES, 'UTF-8');
$medio = htmlspecialchars($request['medio'] ?? '-', ENT_QUOTES, 'UTF-8');
$cargo = htmlspecialchars($request['cargo'] ?? '-', ENT_QUOTES, 'UTF-8');
$rut = htmlspecialchars($request['rut'] ?? '-', ENT_QUOTES, 'UTF-8');
$badgeId = htmlspecialchars((string) ($request['id'] ?? $qrToken), ENT_QUOTES, 'UTF-8');
$logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
$logoSrc = base_url() . '/' . ltrim($logoPath, '/');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Credencial</title>
  <style>
    @page { size: 60mm 110mm; margin: 0; }
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0;
      display: grid;
      place-items: center;
      background: #eef2f7;
      font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      color: #0f172a;
    }
    .badge {
      width: 60mm;
      height: 110mm;
      background: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid rgba(15, 23, 42, 0.08);
      box-shadow: 0 12px 32px rgba(2, 8, 23, 0.18);
      display: grid;
      grid-template-rows: 28mm 1fr 10mm;
    }
    .header {
      background: linear-gradient(120deg, #0a3d91, #0b5ed7);
      color: #fff;
      padding: 10px 12px;
      position: relative;
    }
    .header::after {
      content: "";
      position: absolute;
      right: -12mm;
      top: -8mm;
      width: 40mm;
      height: 26mm;
      background: linear-gradient(135deg, #f28c1b, #ffc13b);
      transform: rotate(18deg);
      border-radius: 12px;
      opacity: 0.95;
    }
    .logo {
      width: 36mm;
      height: 12mm;
      object-fit: contain;
      background: rgba(255,255,255,0.94);
      border-radius: 8px;
      padding: 4px 6px;
      z-index: 1;
      position: relative;
      box-shadow: 0 6px 16px rgba(0,0,0,0.18);
    }
    .header-text {
      margin-top: 6px;
      position: relative;
      z-index: 1;
      line-height: 1.2;
    }
    .header-text .title {
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.3px;
      text-transform: uppercase;
    }
    .header-text .sub {
      font-size: 8px;
      opacity: 0.9;
      margin-top: 2px;
    }
    .body {
      padding: 10px 12px 8px;
      display: grid;
      grid-template-rows: auto auto auto 1fr;
      gap: 6px;
      background: #f5f7fb;
    }
    .badge-title {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.2px;
    }
    .badge-dates {
      font-size: 8px;
      color: #475569;
    }
    .name {
      font-size: 12px;
      font-weight: 900;
      color: #0f172a;
    }
    .meta {
      display: grid;
      gap: 4px;
      font-size: 9px;
      color: #111827;
    }
    .meta span {
      font-weight: 700;
      color: #0f172a;
    }
    .qr {
      margin-top: 6px;
      display: grid;
      place-items: center;
      gap: 6px;
    }
    .qr img {
      width: 30mm;
      height: 30mm;
      border-radius: 10px;
      border: 1px solid rgba(15,23,42,.15);
      background: #fff;
      padding: 4px;
    }
    .qr-label {
      font-size: 8px;
      color: #475569;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .footer {
      background: #f28c1b;
      color: #0b1220;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 12px;
      font-size: 8px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .footer span {
      font-size: 9px;
    }
    .actions {
      margin-top: 16px;
      text-align: center;
    }
    .actions button {
      background: #0b5ed7;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 999px;
      font-weight: 700;
      cursor: pointer;
    }
    @media print {
      body { background: #fff; }
      .actions { display: none; }
      .badge { box-shadow: none; border-radius: 0; border: none; }
    }
  </style>
</head>
<body>
  <div>
    <section class="badge" role="region" aria-label="Credencial de acceso">
      <header class="header">
        <img class="logo" src="<?= $logoSrc ?>" alt="Logo <?= $municipalidadName ?>">
        <div class="header-text">
          <div class="title"><?= $municipalidadName ?></div>
          <div class="sub"><?= $eventTitle ?></div>
        </div>
      </header>
      <div class="body">
        <div class="badge-title">Acreditación de medios</div>
        <div class="badge-dates"><?= $eventDates ?></div>
        <div class="name"><?= $fullName ?></div>
        <div class="meta">
          <div><span>Medio:</span> <?= $medio ?></div>
          <div><span>Cargo:</span> <?= $cargo ?></div>
          <div><span>RUT:</span> <?= $rut ?></div>
          <div><span>ID:</span> <?= $badgeId ?></div>
        </div>
        <div class="qr">
          <div class="qr-label">Escanea tu QR</div>
          <img src="<?= $qrUrl ?>" alt="QR acreditación">
        </div>
      </div>
      <footer class="footer">
        <div>Gafete personal e intransferible</div>
        <span><?= $badgeId ?></span>
      </footer>
    </section>
    <div class="actions">
      <button type="button" onclick="window.print()">Imprimir credencial</button>
    </div>
  </div>
</body>
</html>
