<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(): JsonResponse
    {
       // All Product
       $products = Product::all();

       // Return Json Response
       return response()->json([
          'products' => $products
       ]);
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        if(auth()->user()){
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();

            // Create Product
            $product = Product::create([
                'user_id' => auth()->user()->id,
                'name' => $request->name,
                'image' => $imageName,
                'price' => '$'. number_format($request->price),
                'description' => $request->description
            ]);

            // Save Image in Storage folder
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            // Return Json Response
            return response()->json($product, 200);
        } else {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    public function show(int $id): JsonResponse
    {
        // Product Detail
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message'=>'Product Not Found.'
            ], 404);
        }

        // Return Json Response
        return response()->json($product,200);

    }

    public function update(ProductStoreRequest $request, $id): JsonResponse
    {
        // Find product
        $product = Product::find($id);

        if(!$product){
            return response()->json([
            'message'=>'Product Not Found.'
            ], 404);
        }

        if (auth()->user()->id !== $product->user_id) {
            return response()->json(['message' => 'Action Forbidden']);
        }else{
            $product->name = $request->name;
            $product->price = $request->price;
            $product->description = $request->description;

            if($request->image) {
                // Public storage
                $storage = Storage::disk('public');

                // Old iamge delete
                if($storage->exists($product->image))
                    $storage->delete($product->image);

                // Image name
                $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $product->image = $imageName;

                // Image save in public folder
                $storage->put($imageName, file_get_contents($request->image));
            }

            // Update Product
            $product->save();

            return response()->json($product);
        }
    }

    public function destroy($id): JsonResponse
    {
        // Detail
        $product = Product::find($id);
        if(!$product){
          return response()->json([
             'message'=>'Product Not Found.'
          ],404);
        }

        if (auth()->user()->id !== $product->user_id) {
            return response()->json(['message' => 'Action Forbidden']);
        }else{
            // Public storage
        $storage = Storage::disk('public');

        // Image delete
        if($storage->exists($product->image))
            $storage->delete($product->image);

        // Delete Product
        $product->delete();

        // Return Json Response
        return response()->json([
            'message' => "Product successfully deleted."
        ]);
        }
    }
}
