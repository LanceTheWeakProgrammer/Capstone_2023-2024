<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Exception;

class RatingController extends Controller
{
    public function index()
    {
        try {
            $ratings = Rating::with(['user', 'technician'])->get();
            return response()->json($ratings, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve ratings', 'error' => $e->getMessage()], 500);
        }
    }

    public function showByUserId($userId)
    {
        try {
            $ratings = Rating::with('technician')->where('user_id', $userId)->get();

            if ($ratings->isEmpty()) {
                return response()->json(['message' => 'No ratings found for this user'], 404);
            }

            return response()->json($ratings, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user ratings', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:user_profiles,id',
                'technician_id' => 'required|exists:technician_profiles,id',
                'rating' => 'required|integer|min:1|max:5',
                'feedback' => 'nullable|string',
            ]);
    
            $rating = Rating::create($validatedData);

            $rating->load(['user', 'technician']);
    
            return response()->json([
                'message' => 'Rating created successfully',
                'rating' => $rating,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create rating', 'error' => $e->getMessage()], 500);
        }
    }
    

    public function update(Request $request, $id)
    {
        try {
            $rating = Rating::find($id);

            if (!$rating) {
                return response()->json(['message' => 'Rating not found'], 404);
            }

            $validatedData = $request->validate([
                'rating' => 'integer|min:1|max:5',
                'feedback' => 'nullable|string',
            ]);

            $rating->update($validatedData);

            return response()->json([
                'message' => 'Rating updated successfully',
                'rating' => $rating,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update rating', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $rating = Rating::find($id);

            if (!$rating) {
                return response()->json(['message' => 'Rating not found'], 404);
            }

            $rating->delete();

            return response()->json(['message' => 'Rating deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete rating', 'error' => $e->getMessage()], 500);
        }
    }
}
