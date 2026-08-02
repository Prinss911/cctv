<?php if (($pagination['last_page'] ?? 1) > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center mb-0 mt-3">
        <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= url(strtok($_SERVER['REQUEST_URI'], '?') . '?page=' . ($pagination['current_page'] - 1)) ?>" aria-label="Previous">&laquo;</a>
        </li>
        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= url(strtok($_SERVER['REQUEST_URI'], '?') . '?page=' . $i) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= url(strtok($_SERVER['REQUEST_URI'], '?') . '?page=' . ($pagination['current_page'] + 1)) ?>" aria-label="Next">&raquo;</a>
        </li>
    </ul>
</nav>
<?php endif; ?>