<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Profile\UpdatePreferencesRequest;
use App\Http\Requests\V1\Profile\UpdateProfileRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Responses\V1\ApiResponse;
use App\Models\V1\User;
use Illuminate\Http\Request;

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
        $user->profile->update([
            'preferences' => array_replace_recursive($user->profile->preferences, $request->validated())
        ]);
        return ApiResponse::ok(data: new UserResource($user));
    }
}
