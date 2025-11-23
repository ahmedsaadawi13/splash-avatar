<?php
// FILE: /app/helpers/PaginatorHelper.php

class PaginatorHelper {
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    private $offset;

    public function __construct($totalItems, $itemsPerPage = 20, $currentPage = 1) {
        $this->totalItems = (int) $totalItems;
        $this->itemsPerPage = (int) $itemsPerPage;
        $this->currentPage = max(1, (int) $currentPage);
        $this->totalPages = (int) ceil($this->totalItems / $this->itemsPerPage);
        $this->offset = ($this->currentPage - 1) * $this->itemsPerPage;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function getLimit() {
        return $this->itemsPerPage;
    }

    public function getTotalPages() {
        return $this->totalPages;
    }

    public function getCurrentPage() {
        return $this->currentPage;
    }

    public function hasNextPage() {
        return $this->currentPage < $this->totalPages;
    }

    public function hasPrevPage() {
        return $this->currentPage > 1;
    }

    public function getNextPage() {
        return $this->hasNextPage() ? $this->currentPage + 1 : null;
    }

    public function getPrevPage() {
        return $this->hasPrevPage() ? $this->currentPage - 1 : null;
    }

    public function render($baseUrl = '') {
        if ($this->totalPages <= 1) {
            return '';
        }

        $html = '<div class="pagination">';

        if ($this->hasPrevPage()) {
            $html .= '<a href="' . $baseUrl . '?page=' . $this->getPrevPage() . '" class="page-link">&laquo; Previous</a>';
        }

        for ($i = 1; $i <= $this->totalPages; $i++) {
            if ($i === $this->currentPage) {
                $html .= '<span class="page-link active">' . $i . '</span>';
            } else {
                $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="page-link">' . $i . '</a>';
            }
        }

        if ($this->hasNextPage()) {
            $html .= '<a href="' . $baseUrl . '?page=' . $this->getNextPage() . '" class="page-link">Next &raquo;</a>';
        }

        $html .= '</div>';

        return $html;
    }
}
