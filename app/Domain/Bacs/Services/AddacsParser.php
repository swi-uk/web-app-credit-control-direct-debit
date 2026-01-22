<?php

namespace App\Domain\Bacs\Services;

class AddacsParser
{
    public function parse(string $contents): array
    {
        return $this->parseContents($contents);
    }

    private function parseContents(string $contents): array
    {
        $trimmed = trim($contents);
        if ($trimmed === '') {
            return [];
        }

        $firstChar = $trimmed[0];
        if ($firstChar === '{' || $firstChar === '[') {
            return $this->parseJson($trimmed);
        }

        return $this->parseCsv($trimmed);
    }

    private function parseJson(string $contents): array
    {
        $decoded = json_decode($contents, true);
        if (is_array($decoded)) {
            $records = array_is_list($decoded) ? $decoded : [$decoded];
            return array_map(fn ($row) => $this->normalizeRow($row, null, $row), $records);
        }

        $rows = [];
        foreach (preg_split('/\r\n|\n|\r/', $contents) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $row = json_decode($line, true);
            if (!is_array($row)) {
                continue;
            }
            $rows[] = $this->normalizeRow($row, null, $row);
        }

        return $rows;
    }

    private function parseCsv(string $contents): array
    {
        $lines = preg_split('/\r\n|\n|\r/', $contents);
        $rows = [];
        $header = null;

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $row = str_getcsv($line);
            if ($header === null && $this->looksLikeHeader($row)) {
                $header = array_map('strtolower', $row);
                continue;
            }
            $rows[] = $this->normalizeRow($row, $header, ['raw' => $line]);
        }

        return $rows;
    }

    private function looksLikeHeader(array $row): bool
    {
        $headerTokens = array_map('strtolower', $row);
        $known = ['reference', 'external_order_id', 'amount', 'code', 'description', 'order_id'];
        return count(array_intersect($headerTokens, $known)) > 0;
    }

    private function normalizeRow(array $row, ?array $header, array $raw): array
    {
        $data = [];
        if ($header) {
            foreach ($header as $index => $key) {
                $data[$key] = $row[$index] ?? null;
            }
        } else {
            $data = [
                'reference' => $row[0] ?? null,
                'external_order_id' => $row[1] ?? null,
                'amount' => $row[2] ?? null,
                'code' => $row[3] ?? null,
                'description' => $row[4] ?? null,
            ];
        }

        return [
            'reference' => $data['reference'] ?? null,
            'external_order_id' => $data['external_order_id'] ?? ($data['order_id'] ?? null),
            'amount' => $data['amount'] ?? null,
            'code' => $data['code'] ?? null,
            'description' => $data['description'] ?? null,
            'raw' => $raw,
        ];
    }
}
