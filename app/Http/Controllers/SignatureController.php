<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SignatureController extends Controller
{
    //function to handle the upload of a new signature image, it validates the uploaded file, deletes the old signature if it exists, stores the new signature securely in the storage/app directory, and updates the user's database record with the new signature path.
    public function upload(Request $request)
    {
        $request->validate([
            'signature_image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ], [
            'signature_image.max' => 'The signature image must not be larger than 2MB.'
        ]);

        $user = Auth::user();

        // 1. Clean up the old signature if it exists
        if ($user->e_signature) {
            $oldPath = storage_path('app/' . $user->e_signature);
            if (file_exists($oldPath)) {
                unlink($oldPath); // Native PHP delete
            }
        }

        // 2. Store securely. Passing 'local' ensures it goes to storage/app
        $path = $request->file('signature_image')->store('users/signatures', 'local');

        // 3. Save the relative path to the database
        $user->e_signature = $path;
        $user->save();

        return redirect()->back()->with('success', 'Digital signature securely uploaded.');
    }

    //function to remove the signature permanently.
    public function remove()
    {
        $user = Auth::user();

        if ($user->e_signature) {
            // Get the absolute physical path
            $oldPath = storage_path('app/' . $user->e_signature);

            // Delete the physical file from the secure vault
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            // Nullify the database record
            $user->e_signature = null;
            $user->save();
        }

        return redirect()->back()->with('success', 'Digital signature permanently removed.');
    }

    //function to securely stream a specific user's signature. this function is called for every document and every user, so it includes security checks to ensure that only authorized users can access the signature files. It retrieves the target user's signature path from the database, checks if the physical file exists in the secure storage, and then uses Laravel's response()->file() method to stream the file directly to the browser without exposing the underlying storage structure.
    public function showSpecific($id)
    {
        $viewer = Auth::user();

        // SECURITY: Allow if the viewer is looking at their OWN signature,
        // OR if the viewer is a privileged role looking at someone else's log.
        if ($viewer->id != $id && !in_array($viewer->role, ['secretariat', 'chair', 'researcher', 'Researcher'])) {
            abort(403, 'Unauthorized to view this signature.');
        }

        // Find the specific user from the database
        $targetUser = User::findOrFail($id);

        if (!$targetUser->e_signature) {
            abort(404, 'Signature not found in database.');
        }

        // Get the absolute path to the file inside storage/app
        $fullPath = storage_path('app/' . $targetUser->e_signature);

        // Check if the physical file actually exists
        if (!file_exists($fullPath)) {
            abort(404, 'Signature file missing from server.');
        }

        // Use Laravel's native file response to stream it directly to the browser
        return response()->file($fullPath);
    }
}
