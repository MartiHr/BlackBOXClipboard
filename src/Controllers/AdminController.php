<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';

class AdminController extends Controller
{
    public function index(): void
    {
        AuthService::requireLogin();
        AuthService::requireAdmin();

        $this->render('admin/index', [
            'extraCss'    => ['admin.css'],
            'showNav'     => true,
            'showActions' => true,
            'currentPage' => 'admin',
            'mainClass'   => 'admin-main',
        ], 'Админ панел');
    }

    public function export(): void
    {
        AuthService::requireLogin();
        AuthService::requireAdmin();

        $clipboards = $this->db->query(
            'SELECT id, name, description, owner_id, visibility, mode, share_code, created_at
             FROM clipboards ORDER BY id'
        )->fetchAll(PDO::FETCH_ASSOC);

        $types = $this->db->query(
            'SELECT id, name, mime_type, category FROM resource_types ORDER BY id'
        )->fetchAll(PDO::FETCH_ASSOC);

        $allowed = $this->db->query(
            'SELECT clipboard_id, resource_type_id
             FROM clipboard_allowed_resource_types ORDER BY clipboard_id'
        )->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'exported_at'  => date('Y-m-d H:i:s'),
            'clipboards'   => $clipboards,
            'resource_types' => $types,
            'allowed_resource_types' => $allowed,
        ];

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="clipboard-config-' . date('Y-m-d') . '.json"');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function import(): void
    {
        AuthService::requireLogin();
        AuthService::requireAdmin();

        header('Content-Type: application/json');

        if (empty($_FILES['config_file']) || $_FILES['config_file']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Файлът не е качен']);
            return;
        }

        $raw = file_get_contents($_FILES['config_file']['tmp_name']);
        $data = json_decode($raw, true);

        if (!$data || !isset($data['clipboards'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Невалиден JSON формат']);
            return;
        }

        $this->db->beginTransaction();
        try {
            $this->db->exec('DELETE FROM clipboard_allowed_resource_types');
            $this->db->exec('DELETE FROM clipboards');

            $insertBoard = $this->db->prepare(
                'INSERT INTO clipboards (id, name, description, owner_id, visibility, mode, share_code, created_at)
                 VALUES (:id, :name, :description, :owner_id, :visibility, :mode, :share_code, :created_at)'
            );

            foreach ($data['clipboards'] as $c) {
                $insertBoard->execute([
                    ':id' => $c['id'],
                    ':name' => $c['name'],
                    ':description' => $c['description'] ?? '',
                    ':owner_id' => $c['owner_id'],
                    ':visibility' => $c['visibility'] ?? 'public',
                    ':mode' => $c['mode'] ?? 'multi',
                    ':share_code' => $c['share_code'] ?? null,
                    ':created_at' => $c['created_at'] ?? date('Y-m-d H:i:s'),
                ]);
            }

            if (!empty($data['allowed_resource_types'])) {
                $insertAllowed = $this->db->prepare(
                    'INSERT INTO clipboard_allowed_resource_types (clipboard_id, resource_type_id)
                     VALUES (:clipboard_id, :resource_type_id)'
                );

                foreach ($data['allowed_resource_types'] as $a) {
                    $insertAllowed->execute([
                        ':clipboard_id' => $a['clipboard_id'],
                        ':resource_type_id' => $a['resource_type_id'],
                    ]);
                }
            }

            $this->db->commit();
            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            $this->db->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
