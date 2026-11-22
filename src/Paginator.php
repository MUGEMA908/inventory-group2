<?php
/** Pagination helper: computes LIMIT/OFFSET and renders the page links. */
class Paginator
{
    public int $pages;
    public int $page;

    public function __construct(
        private int $total,
        private int $perPage,
        int $page,
        private string $param = 'page'
    ) {
        $this->pages = max(1, (int) ceil($total / $perPage));
        $this->page  = min(max(1, $page), $this->pages);
    }

    public function limit(): int  { return $this->perPage; }
    public function offset(): int { return ($this->page - 1) * $this->perPage; }

    public function summary(): string
    {
        if ($this->total === 0) {
            return 'No records';
        }
        $from = $this->offset() + 1;
        $to   = min($this->offset() + $this->perPage, $this->total);
        return "Showing {$from}–{$to} of {$this->total}";
    }

    private function url(int $p): string
    {
        $q = $_GET;
        $q[$this->param] = $p;
        return '?' . htmlspecialchars(http_build_query($q), ENT_QUOTES, 'UTF-8');
    }

    public function render(): string
    {
        if ($this->pages <= 1) {
            return '<div class="pager"><span class="pager-info">' . $this->summary() . '</span></div>';
        }
        $html = '<div class="pager"><span class="pager-info">' . $this->summary() . '</span><nav aria-label="Pagination"><ul>';
        $html .= $this->page > 1
            ? '<li><a href="' . $this->url($this->page - 1) . '">Previous</a></li>'
            : '<li><span class="off">Previous</span></li>';

        $start = max(1, $this->page - 2);
        $end   = min($this->pages, $this->page + 2);
        if ($start > 1) {
            $html .= '<li><a href="' . $this->url(1) . '">1</a></li>';
            if ($start > 2) $html .= '<li><span class="off">…</span></li>';
        }
        for ($i = $start; $i <= $end; $i++) {
            $html .= $i === $this->page
                ? '<li><span class="current" aria-current="page">' . $i . '</span></li>'
                : '<li><a href="' . $this->url($i) . '">' . $i . '</a></li>';
        }
        if ($end < $this->pages) {
            if ($end < $this->pages - 1) $html .= '<li><span class="off">…</span></li>';
            $html .= '<li><a href="' . $this->url($this->pages) . '">' . $this->pages . '</a></li>';
        }
        $html .= $this->page < $this->pages
            ? '<li><a href="' . $this->url($this->page + 1) . '">Next</a></li>'
            : '<li><span class="off">Next</span></li>';
        return $html . '</ul></nav></div>';
    }
}
