<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Profile\AvatarUpdateRequest;
use App\Http\Requests\V1\Profile\PreferencesUpdatedRequest;
use App\Http\Requests\V1\Profile\ProfileUpdateRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Responses\V1\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return ApiResponse::ok(data: UserResource::make($request->user()->load('profile')));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user()->load('profile');
        $user->profile->fill($request->validated());

        if ($user->profile->isDirty()) {
            $user->profile->save();
        }
        return ApiResponse::ok(data: UserResource::make($user));
    }

    public function updatePreferences(PreferencesUpdatedRequest $request)
    {
        $user = $request->user()->load('profile');
        $user->profile->forceFill([
            'preferences' => array_replace_recursive($user->profile->preferences, $request->validated())
        ])->save();
        return ApiResponse::ok(data: UserResource::make($user));
    }

    public function updateAvatar(AvatarUpdateRequest $request)
    {
        $file = $request->validated('avatar');
        $user = $request->user()->load('profile');

        if ($user->profile->avatar_url) {
            Storage::disk('public')->delete($user->profile->avatar_url);
        }

        $path = $file->store('avatars', 'public');
        $user->profile->forceFill(['avatar_url' => $path])->save();

        return ApiResponse::ok(data: UserResource::make($user));
    }

    public function deleteAvatar(Request $request)
    {
        $user = $request->user()->load('profile');

        if ($user->profile->avatar_url) {
            Storage::disk('public')->delete($user->profile->avatar_url);
            $user->profile->forceFill(['avatar_url' => null])->save();
        }

        return ApiResponse::noContent();
    }
}
