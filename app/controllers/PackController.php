<?php
// FILE: /app/controllers/PackController.php

class PackController extends Controller {
    public function index() {
        $tenantId = Auth::tenantId();
        $userId = Auth::id();

        $packModel = new AvatarPack();

        if (Auth::isEndUser()) {
            $packs = $packModel->getByUser($userId, $tenantId);
        } else {
            $packs = $packModel->getByTenant($tenantId);
        }

        return $this->view('packs/index', [
            'title' => 'Avatar Packs',
            'packs' => $packs,
        ]);
    }

    public function view($packId) {
        $tenantId = Auth::tenantId();

        $packModel = new AvatarPack();
        $pack = $packModel->getWithAvatars($packId);

        if (!$pack || $pack['tenant_id'] != $tenantId) {
            Session::flash('error', 'Pack not found');
            Response::redirect('/packs');
        }

        return $this->view('packs/view', [
            'title' => $pack['name'],
            'pack' => $pack,
        ]);
    }

    public function download($avatarId) {
        $tenantId = Auth::tenantId();

        $avatarModel = new Avatar();
        $avatar = $avatarModel->find($avatarId);

        if (!$avatar || $avatar['tenant_id'] != $tenantId) {
            Session::flash('error', 'Avatar not found');
            Response::redirect('/packs');
        }

        $avatarModel->incrementDownloadCount($avatarId);

        $this->db->execute(
            "INSERT INTO downloads_log (tenant_id, avatar_id, user_id, ip_address, user_agent, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [$tenantId, $avatarId, Auth::id(), Request::ip(), Request::userAgent()]
        );

        $filePath = __DIR__ . '/../../storage/uploads/' . $avatar['image_path'];
        $fileName = 'avatar_' . $avatarId . '_' . basename($avatar['image_path']);

        Response::download($filePath, $fileName);
    }

    public function downloadPack($packId) {
        $tenantId = Auth::tenantId();

        $packModel = new AvatarPack();
        $pack = $packModel->getWithAvatars($packId);

        if (!$pack || $pack['tenant_id'] != $tenantId) {
            Session::flash('error', 'Pack not found');
            Response::redirect('/packs');
        }

        Session::flash('info', 'Pack download feature - in production, this would create a ZIP file of all avatars');
        Response::redirect('/packs/' . $packId);
    }
}
