<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserServices
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAvatar() {
        $avatar = auth()->user()->avatar;

        if ($avatar) {
            if (!Str::contains($avatar, 'http')) {
                $avatar = Storage::disk('gcs')->url($avatar);
            }
        }

        return $avatar;
    }


    public function update($data)
    {
        $id = auth()->user()->id;
        $oldAvatar = auth()->user()->avatar;

        $avatar = $data['avatar'];
        if ($avatar instanceof UploadedFile) {
            $fileName = 'avatar_' . $id . '_' . Str::random(10);
            $ext = $avatar->getClientOriginalExtension();
            $path = $fileName . '.' . $ext;

            if ($oldAvatar) {
                Storage::disk('gcs')->delete($oldAvatar);
            }

            Storage::disk('gcs')->put('user/' . $path, file_get_contents($avatar), 'public');
            $data['avatar'] = 'user/' . $path;
        } else {
            $data['avatar'] = $oldAvatar;
        }

        return $this->userRepository->update($id, $data);
    }
}
