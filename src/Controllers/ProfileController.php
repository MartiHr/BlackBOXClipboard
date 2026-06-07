<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Auth/AuthService.php';
require_once __DIR__ . '/../Services/ClipboardService.php';
require_once __DIR__ . '/../Repositories/Core/BaseRepository.php';
require_once __DIR__ . '/../Repositories/UserRepository.php';
require_once __DIR__ . '/../Repositories/ClipboardRepository.php';
require_once __DIR__ . '/../Repositories/ResourceRepository.php';

class ProfileController extends Controller
{
    private ClipboardService $service;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->service = new ClipboardService(
            new ClipboardRepository($db),
            new ResourceTypeRepository($db),
            $db
        );
    }

    public function show(): void
    {
        AuthService::requireLogin();

        $userId = $_SESSION['user_id'];
        $subscriptions = $this->service->getSubscriptions($userId);

        $userRepo = new UserRepository($this->db);
        $userData = $userRepo->findById($userId);
        $user = [
            'id'       => $userId,
            'username' => $_SESSION['username'],
            'email'    => $userData['email'] ?? '',
            'role'     => $userData['role'] ?? 'user',
        ];

        $this->render('profile/show', [
            'user'          => $user,
            'subscriptions' => $subscriptions,
            'extraCss'      => ['profile.css'],
            'extraJs'       => ['profile.js'],
            'showNav'       => true,
            'showActions'   => true,
            'currentPage'   => 'profile',
            'mainClass'     => 'profile-main',
        ], 'Профил');
    }
}
