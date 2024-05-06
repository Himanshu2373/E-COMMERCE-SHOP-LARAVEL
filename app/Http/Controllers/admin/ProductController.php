<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\TempImagesController;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\ProductImage;
use App\Models\TempImage;
use Image;

class ProductController extends Controller
{

    public function index(Request $request){
     
        $products=Product::latest('id')->with('product_images');

        if(!empty($request->get('keyword'))){
            $products=$products->where('title','like','%'.$request->get('keyword').'%');

        }

        $products=$products->paginate(10);
       
        $data['products']= $products;
        return view('admin.products.list',$data);

    }
    public function create(){
        $data=[];
        $categories=Category::orderBy('name','ASC')->get();
        $brands=Brand::orderBy('name','ASC')->get();
        $colors=Color::orderBy('name','ASC')->get();
        $sizes=Size::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['brands']=$brands;
        $data['colors']=$colors;
        $data['sizes']=$sizes;
        return view('admin.products.create',$data);
    }

    public function store(Request $request){
        // dd($request->image_array);
        // exit();
        $rules=[
            'title'=>'required',
            'slug'=>'required|unique:products',
            'price'=>'required|numeric',
            'sku'=>'required|unique:products',
            'track_qty'=>'required|in:Yes,No',
            'category'=>'required|numeric',
            'is_featured'=>'required|in:Yes,No',
        ];

        if(!empty($request->track_qty)&& $request->track_qty=='Yes'){
              $rules['qty']='required|numeric';
        }

        $validator=Validator::make($request->all(),$rules);

        if($validator->passes()){

            $products=new Product;
            $products->title=$request->title;
            $products->slug=$request->slug;
            $products->description=$request->description;
            $products->sort_description=$request->sort_description;
            $products->shipping_return=$request->shipping_return;
            $products->price=$request->price;
            $products->compare_price=$request->compare_price;
            $products->category_id=$request->category;
            $products->sub_category_id=$request->sub_category;
            $products->brand_id=$request->brand;
            $products->color_id=$request->color;
            $products->size_id=$request->size;
            $products->is_featured=$request->is_featured;
            $products->sku=$request->sku;
            $products->barcode=$request->barcode;
            $products->track_qty=$request->track_qty;
            $products->qty=$request->qty;
            $products->status=$request->status;
            $products->related_product=(!empty($request->related_products))? implode(',',$request->related_products):'';
            $products->save();


            // save gallery pics
            if(!empty($request->image_array)){
                foreach($request->image_array as $temp_image_id){

                    $tempImageInfo=TempImage::find($temp_image_id);
                    $extArray=explode('.',$tempImageInfo->name);
                    $ext=last($extArray);  //like jpg,gif,png

                    $productsImage=new ProductImage();
                    $productsImage->product_id=$products->id;
                    $productsImage->image='NULL';
                    $productsImage->save();


                    $imageName= $products->id.'-'.$productsImage->id.'-'.time().'-'.$ext;
                    $productsImage->image=$imageName;
                    $productsImage->save();

                   // generate product thumbnail

                   //large image
                    $sourcePath=public_path().'/temp/' .$tempImageInfo->name;
                    $destPath=public_path().'/uploads/product/large/'.$imageName;
                    $image=Image::make($sourcePath);
                    $image->resize(1400,null,function($constraint){
                        $constraint->aspectRatio();
                    }); 
                    $image->save($destPath);

                   //small image

                   $destPath=public_path().'/uploads/product/small/'.$imageName;
                   $image=Image::make($sourcePath);
                   $image->fit(300,300);
                   $image->save($destPath);
                }
            }

            $request->session()->flash('success','Product added successfully');
            return response()->json([
                'status'=>true,
                'message'=>'Product Added Successfully'
            ]);

        }
        else{
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

    }


    public function edit($productId, Request $request)
    {
        $products=Product::find($productId);
        if(empty($products)){
            return redirect()->route('products.index');
        }

        //fetch product image
         $productImages=ProductImage::where('product_id',$products->id)->get();

        $subCategories=SubCategory::where('category_id',$products->category_id)->get();
        $relatedProducts=[];
        // fetch related products
        if($products->related_product != ''){
            $productArray=explode(',',$products->related_product);
            $relatedProducts=Product::whereIn('id',$productArray)->get();
        }


        $data=[];
        $categories=Category::orderBy('name','ASC')->get();
        $brands=Brand::orderBy('name','ASC')->get();
        $colors=Color::orderBy('name','ASC')->get();
        $sizes=Size::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['brands']=$brands;
        $data['colors']=$colors;
        $data['sizes']=$sizes;
        $data['subCategories']=$subCategories;
        $data['productImages']=$productImages;
        $data['relatedProducts']=$relatedProducts;
        $data['products']=$products;
        return view('admin.products.edit',$data);
       
    }

    public function update(Request $request, $productId)
   {
    $products=Product::find($productId);
    if(empty($products)){
        $request->session()->flash('error', 'product not found');
        return response()->json([
           'status'=>false,
           'notFound'=>true,
           'message'=>'product not found'
        ]);
    }

    $rules=[
        'title'=>'required',
        'slug'=>'required|unique:products,slug,'.$products->id.',id',
        'price'=>'required|numeric',
        'sku'=>'required|unique:products,slug,'.$products->id.',id',
        'track_qty'=>'required|in:Yes,No',
        'category'=>'required|numeric',
        'is_featured'=>'required|in:Yes,No',
    ];

    if(!empty($request->track_qty)&& $request->track_qty=='Yes'){
          $rules['qty']='required|numeric';
    }

    $validator=Validator::make($request->all(),$rules);

    if($validator->passes()){

        $products->title=$request->title;
        $products->slug=$request->slug;
        $products->description=$request->description;
        $products->sort_description=$request->sort_description;
        $products->shipping_return=$request->shipping_return;
        $products->price=$request->price;
        $products->compare_price=$request->compare_price;
        $products->category_id=$request->category;
        $products->sub_category_id=$request->sub_category;
        $products->brand_id=$request->brand;
        $products->color_id=$request->color;
        $products->size_id=$request->size;
        $products->is_featured=$request->is_featured;
        $products->sku=$request->sku;
        $products->barcode=$request->barcode;
        $products->track_qty=$request->track_qty;
        $products->qty=$request->qty;
        $products->status=$request->status;
        $products->related_product=(!empty($request->related_products))? implode(',',$request->related_products):'';
        $products->save();


        // save gallery pics
       
        $request->session()->flash('success','Product updated successfully');
        return response()->json([
            'status'=>true,
            'message'=>'Product updated Successfully'
        ]);

    }
    else{
        return response()->json([
            'status'=>false,
            'errors'=>$validator->errors()
        ]);
    }


   }

   public function destroy($id , Request $request){
    $products=Product::find($id);
    if(empty($products)){
        $request->session()->flash('error', 'Product Not found');
        return response()->json([
            'status'=>false,
            'notFound'=>true
        
        ]);
       
    }
     
    $productImages=ProductImage::where('product_id',$id)->get();

    if(!empty($productImages)){
        foreach($productImages as $productImage){
            File::delete(public_path().'/uploads/product/large/'.$productImage->image );
            File::delete(public_path().'/uploads/product/small/'.$productImage->image );

        }
        ProductImage::where('product_id',$id)->delete();
    
    }
   
    $products->delete();
    $request->session()->flash('success', 'Product Deleted Successfully');
    return response()->json([
        'status'=>true,
        'message'=>'Product deleted Successfully'
    ]);
   }

   public function getproduct(Request $request){
       
    $tempProduct=[];
    if($request->term!=''){
        $products=Product::where('title','LIKE','%'.$request->term.'%')->get();

        if($products!=''){
           foreach($products as $product){
               $tempProduct[]=array('id'=>$product->id,'text'=>$product->title);
           }
        }
    }
       return response()->json([
        'tags'=>$tempProduct,
        'status'=>true
       ]);
   }
}
