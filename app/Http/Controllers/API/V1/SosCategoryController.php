<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\SosCategory;
use Illuminate\Http\Request;

class SosCategoryController extends Controller
{
    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'name' => 'required|string|max:255',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('sos_categories', 'public');
            }

            // Create new
            $sos_categories = SosCategory::create([
                'name' => $request->name,
                'image' => $path,
            ]);

            \DB::commit();
            $data = [
                'sos_categories' => $sos_categories,
            ];

            return res(
                status: true,
                message: "Created successfully",
                data: $data,
                code: HTTP_CREATED
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            // Validate input
            $validator = validator($request->all(), [
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|max:5120', // Ensure the image is valid and not too large
            ]);

            if ($validator->fails()) {
                return res(
                    status: false,
                    message: $validator->errors()->first(),
                    code: HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Find the SosCategory by ID
            $sosCategory = SosCategory::where('id', $id);

            if (!$sosCategory) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_NOT_FOUND
                );
            }

            $path = $sosCategory->image; // Keep the old image path if no new image is uploaded

            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($path) {
                    \Storage::disk('public')->delete($path);
                }

                // Store the new image and update the path
                $path = $request->file('image')->store('sos_categories', 'public');
            }

            // Update the SosCategory
            $sosCategory->update([
                'name' => $request->name,
                'image' => $path,
            ]);

            \DB::commit();
            $data = [
                'sos_categories' => $sosCategory
            ];
            return res(
                status: true,
                message: "Updated successfully",
                data: $data,
                code: HTTP_OK
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }



    public function index()
    {
        try {
            // Start building the query
            $sos_categories = SosCategory::all();

            // Check if visitors are found
            if ($sos_categories->isEmpty()) {
                return res(
                    status: false,
                    message: "Not found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'sos_categories' => $sos_categories,
            ];
            return res(
                status: true,
                message: "Data retrieved successfully",
                data: $data,
                code: HTTP_OK
            );

        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return res(
                status: false,
                message: $e->getMessage(),
                code: HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


}
