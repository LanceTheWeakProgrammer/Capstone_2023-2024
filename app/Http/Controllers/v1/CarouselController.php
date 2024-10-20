<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Carousel;

class CarouselController extends Controller
{
    public function index()
    {
        try {
            $carousel = Carousel::all();

            return response()->json([
                'success' => true,
                'data' => $carousel
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve carousel data:' .  $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'carousel_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]); 

        try {
            $randomNumber =mt_rand(1000000, 9999999);

            $imageName = 'IMG_' . $randomNumber . '.' . $request->file('carousel_image')->getClientOriginalExtension();

            $imagePath = $request->file('carousel_image')->storeAs('images/carousel', $imageName, 'public');

            $carousel = Carousel::create([
                'carousel_image' => $imagePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Carousel image added successfully.',
                'data' => $carousel
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add image:' . $e->getMessage()
            ], 500);
        }
    }

    public function toggle($id) 
    {
        try {
            $carousel = Carousel::findOrFail($id);

            $carousel->active = $carousel->active ? 0 : 1;
            
            $carousel->save();

            $message = $carousel->active 
                ? 'Image set to active successfully.' 
                : 'Image set to inactive successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'carousel' => $carousel
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status:' . $e->getMessage()
            ], 500);
        }
    }
}
