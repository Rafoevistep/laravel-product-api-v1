<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'review' => 'required|string',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $findReview = Review::where(['user_id' => auth()->user()->id, 'product_id' => $request->product_id])->first();

        if ($findReview > 0) {
            return response()->json(['message' => 'You already reviewed this product']);
        } else {
            $review = new Review;
            $review->review = $request->review;
            $review->rating = $request->rating;
            $review->user_id = auth()->user()->id;
            $product->reviews()->save($review);

            return response()->json(['message' => 'Review Added', 'review' => $review]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  Review $review)
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @param  \App\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Review $review)
    {
        if (auth()->user()->id !== $review->user_id) {
            return response()->json(['message' => 'Action Forbidden']);
        }
        $review->delete();
        return response()->json(null, 204);
    }
}


