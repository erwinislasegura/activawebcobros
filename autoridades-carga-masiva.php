<?php
require __DIR__ . '/app/bootstrap.php';

$bulkErrors = [];
$bulkSuccess = '';

function excel_serial_to_date($value): ?string
{
    if (!is_numeric($value)) {
        return null;
    }
    $serial = (int) $value;
    if ($serial <= 0) {
        return null;
    }
    $base = new DateTime('1899-12-30');
    $base->modify('+' . $serial . ' days');
    return $base->format('Y-m-d');
}

function parse_excel_sheet(string $path): array
{
    $rows = [];
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        return $rows;
    }

    $sharedStrings = [];
    $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedXml !== false) {
        $shared = simplexml_load_string($sharedXml);
        if ($shared) {
            foreach ($shared->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } else {
                    $text = '';
                    foreach ($si->r as $run) {
                        $text .= (string) $run->t;
                    }
                    $sharedStrings[] = $text;
                }
            }
        }
    }

    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    if ($sheetXml === false) {
        $zip->close();
        return $rows;
    }

    $sheet = simplexml_load_string($sheetXml);
    if (!$sheet) {
        $zip->close();
        return $rows;
    }

    foreach ($sheet->sheetData->row as $row) {
        $rowData = [];
        foreach ($row->c as $cell) {
            $cellRef = (string) $cell['r'];
            $column = preg_replace('/\d+/', '', $cellRef);
            $columnIndex = ord($column) - ord('A');
            $value = '';
            if (isset($cell->v)) {
                $value = (string) $cell->v;
                $type = (string) $cell['t'];
                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }
            } elseif (isset($cell->is->t)) {
                $value = (string) $cell->is->t;
            }
            $rowData[$columnIndex] = $value;
        }
        if (!empty($rowData)) {
            ksort($rowData);
            $rows[] = array_values($rowData);
        }
    }

    $zip->close();
    return $rows;
}

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS authority_groups (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            nombre VARCHAR(120) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY authority_groups_nombre_unique (nombre)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

if (isset($_GET['action']) && $_GET['action'] === 'download-template') {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="plantilla-autoridades.xlsx"');

    $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
    $zip = new ZipArchive();
    $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    $sheetXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" t="inlineStr"><is><t>nombre</t></is></c>
      <c r="B1" t="inlineStr"><is><t>grupo</t></is></c>
      <c r="C1" t="inlineStr"><is><t>correo</t></is></c>
      <c r="D1" t="inlineStr"><is><t>telefono</t></is></c>
      <c r="E1" t="inlineStr"><is><t>fecha_inicio</t></is></c>
      <c r="F1" t="inlineStr"><is><t>fecha_fin</t></is></c>
      <c r="G1" t="inlineStr"><is><t>estado</t></is></c>
    </row>
    <row r="2">
      <c r="A2" t="inlineStr"><is><t>Juan Perez</t></is></c>
      <c r="B2" t="inlineStr"><is><t>Concejo Municipal</t></is></c>
      <c r="C2" t="inlineStr"><is><t>juan.perez@municipalidad.cl</t></is></c>
      <c r="D2" t="inlineStr"><is><t>+56 9 1234 5678</t></is></c>
      <c r="E2" t="inlineStr"><is><t>2024-01-01</t></is></c>
      <c r="F2" t="inlineStr"><is><t></t></is></c>
      <c r="G2" t="inlineStr"><is><t>1</t></is></c>
    </row>
  </sheetData>
</worksheet>
XML;

    $zip->addFromString('[Content_Types].xml', <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>
XML);
    $zip->addFromString('_rels/.rels', <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML);
    $zip->addFromString('xl/workbook.xml', <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Autoridades" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>
XML);
    $zip->addFromString('xl/_rels/workbook.xml.rels', <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>
XML);
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
    $zip->close();

    readfile($tempFile);
    unlink($tempFile);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_upload' && verify_csrf($_POST['csrf_token'] ?? null)) {
    if (!isset($_FILES['autoridades_excel']) || $_FILES['autoridades_excel']['error'] !== UPLOAD_ERR_OK) {
        $bulkErrors[] = 'Selecciona un archivo Excel válido.';
    } else {
        $extension = strtolower(pathinfo($_FILES['autoridades_excel']['name'], PATHINFO_EXTENSION));
        if ($extension !== 'xlsx') {
            $bulkErrors[] = 'El archivo debe estar en formato Excel (.xlsx).';
        } else {
            $rows = parse_excel_sheet($_FILES['autoridades_excel']['tmp_name']);
            if (empty($rows)) {
                $bulkErrors[] = 'El archivo está vacío o no tiene una hoja válida.';
            } else {
                $expected = ['nombre', 'grupo', 'correo', 'telefono', 'fecha_inicio', 'fecha_fin', 'estado'];
                $header = array_map('trim', $rows[0]);
                $normalizedHeader = array_map('strtolower', $header);
                $columnMap = [];
                foreach ($normalizedHeader as $index => $column) {
                    if ($column !== '') {
                        $columnMap[$column] = $index;
                    }
                }
                $missing = array_diff($expected, array_keys($columnMap));
                if (!empty($missing)) {
                    $bulkErrors[] = 'Faltan columnas requeridas: ' . implode(', ', $missing) . '. Descarga la plantilla para usar el formato correcto.';
                } else {
                    $groups = db()->query('SELECT id, nombre FROM authority_groups')->fetchAll();
                    $groupMap = [];
                    foreach ($groups as $group) {
                        $key = function_exists('mb_strtolower') ? mb_strtolower($group['nombre']) : strtolower($group['nombre']);
                        $groupMap[$key] = (int) $group['id'];
                    }
                    $validRows = [];
                    for ($i = 1; $i < count($rows); $i++) {
                        $rowNumber = $i + 1;
                        $row = $rows[$i];
                        $nombre = trim((string) ($row[$columnMap['nombre']] ?? ''));
                        $grupo = trim((string) ($row[$columnMap['grupo']] ?? ''));
                        $correo = trim((string) ($row[$columnMap['correo']] ?? ''));
                        $telefono = trim((string) ($row[$columnMap['telefono']] ?? ''));
                        $fechaInicio = trim((string) ($row[$columnMap['fecha_inicio']] ?? ''));
                        $fechaFin = trim((string) ($row[$columnMap['fecha_fin']] ?? ''));
                        $estadoRaw = trim((string) ($row[$columnMap['estado']] ?? ''));

                        if ($nombre === '' && $fechaInicio === '' && $correo === '' && $telefono === '' && $fechaFin === '' && $estadoRaw === '') {
                            continue;
                        }
                        if ($nombre === '' || $fechaInicio === '') {
                            $bulkErrors[] = "Fila {$rowNumber}: faltan campos obligatorios (nombre o fecha_inicio).";
                            continue;
                        }
                        $groupId = null;
                        if ($grupo !== '') {
                            $groupKey = function_exists('mb_strtolower') ? mb_strtolower($grupo) : strtolower($grupo);
                            $groupId = $groupMap[$groupKey] ?? null;
                            if (!$groupId) {
                                $bulkErrors[] = "Fila {$rowNumber}: el grupo \"{$grupo}\" no existe.";
                                continue;
                            }
                        }
                        $tipo = $groupId ? $grupo : 'Sin grupo';
                        if ($fechaInicio !== '' && is_numeric($fechaInicio)) {
                            $fechaInicio = excel_serial_to_date($fechaInicio) ?? $fechaInicio;
                        }
                        $inicio = DateTime::createFromFormat('Y-m-d', $fechaInicio);
                        if (!$inicio || $inicio->format('Y-m-d') !== $fechaInicio) {
                            $bulkErrors[] = "Fila {$rowNumber}: fecha_inicio inválida (usa YYYY-MM-DD).";
                            continue;
                        }
                        if ($fechaFin !== '' && is_numeric($fechaFin)) {
                            $fechaFin = excel_serial_to_date($fechaFin) ?? $fechaFin;
                        }
                        $fechaFin = $fechaFin !== '' ? $fechaFin : null;
                        if ($fechaFin !== null) {
                            $fin = DateTime::createFromFormat('Y-m-d', $fechaFin);
                            if (!$fin || $fin->format('Y-m-d') !== $fechaFin) {
                                $bulkErrors[] = "Fila {$rowNumber}: fecha_fin inválida (usa YYYY-MM-DD).";
                                continue;
                            }
                        }
                        $estado = in_array(strtolower($estadoRaw), ['0', 'deshabilitado', 'inactivo'], true) ? 0 : 1;
                        $validRows[] = [
                            $nombre,
                            $tipo,
                            $correo !== '' ? $correo : null,
                            $telefono !== '' ? $telefono : null,
                            $fechaInicio,
                            $fechaFin,
                            $estado,
                            $groupId,
                        ];
                    }
                    if (!empty($bulkErrors)) {
                        $bulkErrors[] = 'Corrige los errores en el archivo antes de cargar.';
                    } elseif (!empty($validRows)) {
                        $stmtInsert = db()->prepare(
                            'INSERT INTO authorities (nombre, tipo, correo, telefono, fecha_inicio, fecha_fin, estado, group_id)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                        );
                        foreach ($validRows as $data) {
                            $stmtInsert->execute($data);
                        }
                        $bulkSuccess = "Se cargaron " . count($validRows) . " autoridades correctamente.";
                    } else {
                        $bulkErrors[] = 'No se encontraron filas válidas para importar.';
                    }
                }
            }
        }
    }
}

$autoridades = db()->query(
    'SELECT a.id, a.nombre, a.tipo, a.fecha_inicio, a.fecha_fin, a.correo, a.estado,
            g.nombre AS grupo, g.id AS grupo_id
     FROM authorities a
     LEFT JOIN authority_groups g ON g.id = a.group_id
     ORDER BY a.fecha_inicio DESC'
)->fetchAll();

$groupPalette = [
    'bg-primary-subtle text-primary',
    'bg-success-subtle text-success',
    'bg-warning-subtle text-warning',
    'bg-info-subtle text-info',
    'bg-danger-subtle text-danger',
    'bg-secondary-subtle text-secondary',
];

function group_badge_class(?int $groupId, array $palette): string
{
    if (!$groupId) {
        return 'bg-light text-muted';
    }
    $index = $groupId % count($palette);
    return $palette[$index];
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Carga masiva de autoridades"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = "Autoridades"; $title = "Carga masiva"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Carga masiva de autoridades</h5>
                                    <p class="text-muted mb-0">Importa autoridades asignando el grupo o tipo desde la plantilla.</p>
                                </div>
                                <a class="btn btn-sm btn-outline-primary" href="autoridades-carga-masiva.php?action=download-template">Descargar plantilla</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($bulkErrors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($bulkErrors as $bulkError) : ?>
                                            <div><?php echo htmlspecialchars($bulkError, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($bulkSuccess !== '') : ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($bulkSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <form method="post" enctype="multipart/form-data" class="row gy-2 align-items-end">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="bulk_upload">
                                    <div class="col-md-8">
                                        <label class="form-label" for="autoridades-excel">Archivo Excel (.xlsx)</label>
                                        <input type="file" id="autoridades-excel" name="autoridades_excel" class="form-control" accept=".xlsx">
                                        <div class="form-text">Columnas requeridas: nombre, grupo, correo, telefono, fecha_inicio, fecha_fin, estado.</div>
                                    </div>
                                    <div class="col-md-4 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Subir masivamente</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Listado de autoridades</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Autoridad</th>
                                                <th>Grupo / Tipo</th>
                                                <th>Periodo</th>
                                                <th>Contacto</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($autoridades)) : ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No hay autoridades registradas.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($autoridades as $autoridad) : ?>
                                                    <?php
                                                    $badgeClass = group_badge_class(isset($autoridad['grupo_id']) ? (int) $autoridad['grupo_id'] : null, $groupPalette);
                                                    $grupoLabel = $autoridad['grupo'] ?? 'Sin grupo';
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($autoridad['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $badgeClass; ?>">
                                                                <?php echo htmlspecialchars($grupoLabel, ENT_QUOTES, 'UTF-8'); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($autoridad['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($autoridad['fecha_fin'] ?? 'Vigente', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($autoridad['correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ((int) $autoridad['estado'] === 1) : ?>
                                                                <span class="badge text-bg-success">Habilitado</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Deshabilitado</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
