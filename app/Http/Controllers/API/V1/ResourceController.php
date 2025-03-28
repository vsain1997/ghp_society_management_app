<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\PolicyTerms;
use App\Models\Slider;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    //
    public function sliders(Request $request)
    {

        try {
            // Start building the query
            $sliders = Slider::all();

            // Check if notices are found
            if ($sliders->isEmpty()) {
                return res(
                    status: false,
                    message: "No slider found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'sliders' => $sliders,
            ];
            return res(
                status: true,
                message: "Slider retrived successfully",
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

    public function privacyPolicy(Request $request)
    {
        try {
            // Start building the query
            $privacy_policy = PolicyTerms::where('name', 'privacy_policy')->first();

            // Check if notices are found
            if (!$privacy_policy) {
                return res(
                    status: false,
                    message: "No content found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'privacy_policy' => $privacy_policy,
            ];
            return res(
                status: true,
                message: "Privacy policy retrived successfully",
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

    public function termsUse(Request $request)
    {
        try {
            // Start building the query
            $terms_use = PolicyTerms::where('name', 'terms_use')->first();

            // Check if notices are found
            if (!$terms_use) {
                return res(
                    status: false,
                    message: "No content found!",
                    code: HTTP_OK
                );
            }

            // Return the response in JSON format
            $data = [
                'terms_of_use' => $terms_use,
            ];
            return res(
                status: true,
                message: "Terms of use retrived successfully",
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
