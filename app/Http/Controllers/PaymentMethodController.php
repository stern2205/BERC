<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    //this function gets all the payment methods in the database and returns them as a JSON response, ordered by active status and then alphabetically by name. This is used to populate the list of payment methods in the frontend, allowing users to see which methods are available and their details such as account number, account name, and whether they are active or not.
    public function index()
    {
        // Get all methods, ordered by active first, then alphabetically
        $methods = PaymentMethod::orderBy('is_active', 'desc')
                                ->orderBy('name', 'asc')
                                ->get();

        return response()->json([
            'success' => true,
            'data'    => $methods
        ]);
    }

    /**
     * 2. STORE NEW METHOD
     * Handles the "Add Payment Method" modal submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'icon_label'     => 'nullable|string|max:3',
            'account_number' => 'required|string|max:255',
            'account_name'   => 'required|string|max:255',
            'bg_color'       => 'nullable|string|max:20',
            'is_active'      => 'required|in:true,false,1,0',
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048',
        ]);

        // Convert string 'true'/'false' from JS FormData to actual boolean
        $validated['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . Str::slug($validated['name']) . '.' . $file->extension();
            $destinationPath = public_path('images/payment_methods');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $validated['logo_path'] = 'images/payment_methods/' . $fileName;
        } else {
            // Default background color if none provided and no image
            $validated['bg_color'] = $validated['bg_color'] ?? '#213C71';
        }

        $method = PaymentMethod::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment method added successfully.',
            'data'    => $method
        ]);
    }

    /**
     * 3. UPDATE EXISTING METHOD
     * Handles the "Confirm Save" modal submission.
     */
    public function update(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'icon_label'     => 'nullable|string|max:3',
            'account_number' => 'required|string|max:255',
            'account_name'   => 'required|string|max:255',
            'bg_color'       => 'nullable|string|max:20',
            'is_active'      => 'required|in:true,false,1,0',
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048',
        ]);

        $validated['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);

        if ($request->has('remove_logo') && $request->remove_logo == 'true') {
            if ($method->logo_path && File::exists(public_path($method->logo_path))) {
                File::delete(public_path($method->logo_path));
            }
            $validated['logo_path'] = null; // Set the database column to null
        }
        // -----------------------------------------------

        // Handle Logo Upload and delete old logo (if a new one is replacing it)
        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($method->logo_path && File::exists(public_path($method->logo_path))) {
                File::delete(public_path($method->logo_path));
            }

            // Save new logo
            $file = $request->file('logo');
            $fileName = time() . '_' . Str::slug($validated['name']) . '.' . $file->extension();
            $file->move(public_path('images/payment_methods'), $fileName);
            $validated['logo_path'] = 'images/payment_methods/' . $fileName;
        }

        $method->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment method updated successfully.',
            'data'    => $method
        ]);
    }

    /**
     * 4. DELETE METHOD
     * Handles the "Delete" modal submission.
     */
    public function destroy($id)
    {
        $method = PaymentMethod::findOrFail($id);

        // Delete associated image file from the server
        if ($method->logo_path && File::exists(public_path($method->logo_path))) {
            File::delete(public_path($method->logo_path));
        }

        $method->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment method deleted successfully.'
        ]);
    }
}
