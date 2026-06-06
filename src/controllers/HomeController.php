<?php

require_once __DIR__ . '/Controller.php';

class HomeController extends Controller 
{
    public function index(): void 
    {
        $clipboards = [];

        $syncJs = <<<'JS'
<script>
const syncBtn = document.getElementById('syncBtn');
if (syncBtn) {
    syncBtn.addEventListener('click', function() {
        const icon = this.querySelector('.material-symbols-outlined');
        icon.style.transform = 'rotate(360deg)';
        icon.style.transition = 'transform 0.5s ease-in-out';
        setTimeout(() => {
            icon.style.transform = 'rotate(0deg)';
            icon.style.transition = 'none';
        }, 500);
    });
}
</script>
JS;

        $this->render('home/index', [
            'clipboards'   => $clipboards,
            'extraCss'     => ['channels.css'],
            'showNav'      => true,
            'showActions'  => true,
            'currentPage'  => 'home',
            'mainClass'    => 'dashboard-main',
            'extraScripts' => $syncJs,
        ], 'Канали');
    }
}