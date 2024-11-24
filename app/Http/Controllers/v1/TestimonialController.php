<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Exception;

class TestimonialController extends Controller
{
    public function index()
    {
        try {
            $testimonials = Testimonial::with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $testimonials,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch testimonials.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'author_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'content' => 'required|string',
                'rating' => 'required|integer|min:1|max:5',
            ]);

            $testimonial = Testimonial::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Testimonial submitted successfully and is awaiting approval.',
                'data' => $testimonial,
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save the testimonial.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approve($id)
    {
        try {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->is_approved = true;
            $testimonial->save();

            return response()->json([
                'success' => true,
                'message' => 'Testimonial approved successfully.',
                'data' => $testimonial,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve the testimonial.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
