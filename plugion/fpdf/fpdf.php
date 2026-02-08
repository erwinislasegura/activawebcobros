<?php

class FPDF
{
    private const DEFAULT_FONT = 'Helvetica';
    private const PAGE_WIDTH = 210.0;
    private const PAGE_HEIGHT = 297.0;

    private float $k;
    private float $x = 10.0;
    private float $y = 10.0;
    private float $fontSize = 12.0;
    private string $font = self::DEFAULT_FONT;
    private string $buffer = '';
    private bool $pageAdded = false;

    public function __construct()
    {
        $this->k = 72 / 25.4;
    }

    public function AddPage(): void
    {
        $this->pageAdded = true;
        $this->x = 10.0;
        $this->y = 10.0;
        $this->buffer = '';
    }

    public function SetFont(string $family, string $style = '', float $size = 12.0): void
    {
        $this->font = $family !== '' ? $family : self::DEFAULT_FONT;
        $this->fontSize = $size > 0 ? $size : 12.0;
    }

    public function Cell(float $w, float $h, string $txt = '', int $border = 0, int $ln = 0, string $align = ''): void
    {
        $text = $this->escape($txt);
        $x = $this->x;
        $y = $this->y;
        if ($align === 'C') {
            $x = $this->x + ($w / 2);
        } elseif ($align === 'R') {
            $x = $this->x + $w;
        }

        $this->buffer .= sprintf(
            "BT /F1 %.2F Tf %.2F %.2F Td (%s) Tj ET\n",
            $this->fontSize,
            $this->mmToPt($x),
            $this->mmToPt(self::PAGE_HEIGHT - $y),
            $text
        );

        if ($ln > 0) {
            $this->Ln($h);
        } else {
            $this->x += $w;
        }
    }

    public function Ln(float $h = 0.0): void
    {
        $lineHeight = $h > 0 ? $h : ($this->fontSize / $this->k) + 2;
        $this->y += $lineHeight;
        $this->x = 10.0;
    }

    public function Output(string $dest = 'I', string $name = 'documento.pdf'): void
    {
        if (!$this->pageAdded) {
            $this->AddPage();
        }

        $objects = [];
        $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[] = sprintf(
            "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>",
            $this->mmToPt(self::PAGE_WIDTH),
            $this->mmToPt(self::PAGE_HEIGHT)
        );
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $stream = "q\n" . $this->buffer . "Q\n";
        $objects[] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPosition . "\n%%EOF";

        if ($dest === 'S') {
            return $pdf;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $name . '"');
        echo $pdf;
        exit;
    }

    private function mmToPt(float $value): float
    {
        return $value * $this->k;
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
