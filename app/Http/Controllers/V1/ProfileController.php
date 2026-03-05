<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Profile\UpdateAvatarRequest;
use App\Http\Requests\V1\Profile\UpdatePreferencesRequest;
use App\Http\Requests\V1\Profile\UpdateProfileRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Responses\V1\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return ApiResponse::ok(data: new UserResource($request->user()->load('profile')));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user()->load('profile');
        $user->profile->fill($request->validated());

        if ($user->profile->isDirty()) {
            $user->profile->save();
        }
        return ApiResponse::ok(data: new UserResource($user));
    }

    public function updatePreferences(UpdatePreferencesRequest $request)
    {
        $user = $request->user()->load('profile');
        $user->profile->forceFill([
            'preferences' => array_replace_recursive($user->profile->preferences, $request->validated())
        ])->save();
        return ApiResponse::ok(data: new UserResource($user));
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $file = $request->validated('avatar');
        $user = $request->user()->load('profile');

        if ($user->profile->avatar_url) {
            Storage::disk('public')->delete($user->profile->avatar_url);
        }

        $path = $file->store('avatars', 'public');
        $user->profile->forceFill(['avatar_url' => $path])->save();

        return ApiResponse::ok(data: new UserResource($user));
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
