<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(): JsonResponse
    {
        // All Product
        $review = Review::all();

        // Return Json Response
        return response()->json([
            'review' => $review
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        // Product Detail
        $review =  Review::find($id);
        if (!$review) {
            return response()->json([
                'message' => 'Review Not Found.'
            ], 404);
        }
        return response()->json([
            'review' => $review
        ], 200);

    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'review' => 'required|string',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $userId = auth()->user()->id;

        $findReview = Review::where(['user_id' => $userId, 'product_id' => $product->id])->first();

        if ($findReview) {
            return response()->json(['message' => 'You already reviewed this product']);
        }

        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'review' => $request->review,
            'rating' => $request->rating
        ]);

        return response()->json($review);
    }


    public function update(Request $request,  Review $review): JsonResponse
    {
        if (auth()->user()->id !== $review->user_id) {
            return response()->json(['message' => 'Action Forbidden']);
        }
        $request->validate([
            'review' => 'required|string',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->save();

        return response()->json(['message' => 'Review Updated', 'review' => $review]);
    }

    public function destroy(Product $product, Review $review): JsonResponse
    {
        if (auth()->user()->id !== $review->user_id) {
            return response()->json(['message' => 'Action Forbidden']);
        }
        $review->delete();
        return response()->json(['message' => 'Review Deleted', 'review' => $review]);
    }
}
