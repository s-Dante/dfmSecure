<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SinisterMultimedia;
use App\Models\User;
use App\Models\Policy;

class MediaController extends Controller
{
    /**
     * Sirve el blob de un archivo multimedia de siniestro.
     * Aplica la misma lógica de permisos que SinisterController.
     */
    public function sinister(Request $request, int $id): Response
    {
        $media    = SinisterMultimedia::findOrFail($id);
        $sinister = $media->sinister;
        $user     = $request->user();

        // Autorización por rol
        if ($user->isInsured()) {
            $ownPolicyIds = Policy::where('insured_id', $user->id)->pluck('id');
            abort_unless($ownPolicyIds->contains($sinister->policy_id), 403);
        } elseif ($user->isAdjuster()) {
            abort_unless($sinister->adjuster_id === $user->id, 403);
        }
        // Supervisor y Admin pueden ver todo

        $blob = $this->readBlob($media->blob_file);
        abort_if(empty($blob), 404);

        $mime = $media->mime ?? 'application/octet-stream';

        return response($blob, 200, [
            'Content-Type'        => $mime,
            'Content-Length'      => strlen($blob),
            'Cache-Control'       => 'private, max-age=3600',
            'Content-Disposition' => 'inline; filename="media_' . $id . '"',
        ]);
    }

    /**
     * Sirve el blob de foto de perfil de un usuario.
     */
    public function profile(Request $request, int $userId): Response
    {
        // Solo el propio usuario o admin puede ver otra foto de perfil
        $authUser = $request->user();
        if ($authUser->id !== $userId && !$authUser->isAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($userId);
        $blob = $this->readBlob($user->profile_picture_blob);
        abort_if(empty($blob), 404);

        // Intentamos detectar el mime del blob
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->buffer($blob) ?: 'image/jpeg';

        return response($blob, 200, [
            'Content-Type'        => $mime,
            'Content-Length'      => strlen($blob),
            'Cache-Control'       => 'private, max-age=3600',
            'Content-Disposition' => 'inline; filename="profile_' . $userId . '"',
        ]);
    }

    /**
     * Lee un LONGBLOB de MySQL que PHP/PDO puede devolver como stream o string.
     */
    private function readBlob(mixed $blob): string
    {
        if (is_null($blob)) {
            return '';
        }
        // PDO devuelve LONGBLOB como resource (stream) en algunos drivers
        if (is_resource($blob)) {
            return stream_get_contents($blob) ?: '';
        }
        return (string) $blob;
    }
}
