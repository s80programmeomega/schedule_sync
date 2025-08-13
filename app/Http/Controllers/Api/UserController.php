<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends ApiController
{
public function profile(Request $request): JsonResponse
{
return $this->successResponse($request->user(), 'Profile retrieved successfully');
}

public function updateProfile(Request $request): JsonResponse
{
try {
$validated = $request->validate([
'name' => 'sometimes|required|string|max:255',
'username' => 'sometimes|required|string|max:255|unique:users,username,' . $request->user()->id,
'bio' => 'nullable|string|max:500',
'timezone' => 'nullable|string|max:255',
]);

$request->user()->update($validated);

return $this->successResponse($request->user()->fresh(), 'Profile updated successfully');
} catch (ValidationException $e) {
return $this->validationErrorResponse($e->errors());
}
}

public function uploadAvatar(Request $request): JsonResponse
{
try {
$validated = $request->validate([
'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
]);

$path = $request->file('avatar')->store('avatars', 'public');

// Delete old avatar
if ($request->user()->avatar) {
Storage::disk('public')->delete($request->user()->avatar);
}

$request->user()->update(['avatar' => $path]);

return $this->successResponse([
'avatar_url' => Storage::url($path)
], 'Avatar uploaded successfully');
} catch (ValidationException $e) {
return $this->validationErrorResponse($e->errors());
}
}

public function updatePassword(Request $request): JsonResponse
{
try {
$validated = $request->validate([
'current_password' => 'required',
'password' => 'required|string|min:8|confirmed',
]);

if (!Hash::check($validated['current_password'], $request->user()->password)) {
return $this->errorResponse('Current password is incorrect', 422);
}

$request->user()->update([
'password' => Hash::make($validated['password'])
]);

return $this->successResponse(null, 'Password updated successfully');
} catch (ValidationException $e) {
return $this->validationErrorResponse($e->errors());
}
}
}
