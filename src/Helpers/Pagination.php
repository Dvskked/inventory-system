<?php

declare(strict_types=1);

namespace InventoryFlow\Helpers;

/**
 * Pagination helper
 */
class Pagination
{
    private int $total;
    private int $currentPage;
    private int $perPage;
    private int $totalPages;

    public function __construct(int $total, int $currentPage = 1, int $perPage = 15)
    {
        $this->total = max(0, $total);
        $this->perPage = max(1, $perPage);
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = max(1, (int) ceil($this->total / $this->perPage));

        if ($this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }
    }

    /**
     * Get offset for database query
     */
    public function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
     * Get limit for database query
     */
    public function getLimit(): int
    {
        return $this->perPage;
    }

    /**
     * Check if has previous page
     */
    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Check if has next page
     */
    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Get previous page number
     */
    public function prevPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    /**
     * Get next page number
     */
    public function nextPage(): int
    {
        return min($this->totalPages, $this->currentPage + 1);
    }

    /**
     * Get page range to display
     */
    public function getRange(int $surrounding = 2): array
    {
        $range = [];
        $start = max(1, $this->currentPage - $surrounding);
        $end = min($this->totalPages, $this->currentPage + $surrounding);

        for ($i = $start; $i <= $end; $i++) {
            $range[] = $i;
        }

        return $range;
    }

    /**
     * Render pagination HTML
     */
    public function render(string $baseUrl = '?'): string
    {
        if ($this->totalPages <= 1) {
            return '';
        }

        $html = '<nav aria-label="Navegacion de paginas">';
        $html .= '<ul class="pagination">';

        // Previous button
        if ($this->hasPrev()) {
            $html .= sprintf(
                '<li class="page-item"><a class="page-link" href="%s%d">&laquo; Anterior</a></li>',
                $baseUrl,
                $this->prevPage()
            );
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>';
        }

        // Page numbers
        foreach ($this->getRange() as $page) {
            if ($page === $this->currentPage) {
                $html .= sprintf(
                    '<li class="page-item active"><span class="page-link">%d</span></li>',
                    $page
                );
            } else {
                $html .= sprintf(
                    '<li class="page-item"><a class="page-link" href="%s%d">%d</a></li>',
                    $baseUrl,
                    $page,
                    $page
                );
            }
        }

        // Next button
        if ($this->hasNext()) {
            $html .= sprintf(
                '<li class="page-item"><a class="page-link" href="%s%d">Siguiente &raquo;</a></li>',
                $baseUrl,
                $this->nextPage()
            );
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Siguiente &raquo;</span></li>';
        }

        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }

    /**
     * Get pagination info text
     */
    public function getInfoText(): string
    {
        $from = $this->getOffset() + 1;
        $to = min($this->getOffset() + $this->perPage, $this->total);

        return "Mostrando {$from} a {$to} de {$this->total} registros";
    }

    /**
     * Get all pagination data
     */
    public function toArray(): array
    {
        return [
            'total'        => $this->total,
            'per_page'     => $this->perPage,
            'current_page' => $this->currentPage,
            'total_pages'  => $this->totalPages,
            'has_prev'     => $this->hasPrev(),
            'has_next'     => $this->hasNext(),
            'from'         => $this->getOffset() + 1,
            'to'           => min($this->getOffset() + $this->perPage, $this->total),
        ];
    }
}
