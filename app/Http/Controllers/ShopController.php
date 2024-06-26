<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\subCategory;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug=null, $subCategorySlug=null){

        $categorySelected='';
        $subcategorySelected='';
        $brandsArray= [];


        $categories=Category::orderBy('name','ASC')->where('showHome','Yes')->with('sub_category')->where('status',1)->get();
        $brands=Brand::orderBy('name','ASC')->where('status',1)->get();
        $products=Product::where('status',1);

        // apply filter here
        if(!empty($categorySlug))
        {
            $category=Category::where('slug', $categorySlug)->first();
            $products= $products->where('category_id', $category->id);
            $categorySelected= $category->id;
        }
        if(!empty($subCategorySlug))
        {
            $subcategory=subCategory::where('slug', $subCategorySlug)->first();
            $products= $products->where('sub_category_id', $subcategory->id);
            $subcategorySelected= $subcategory->id;
        }

        if(!empty($request->get('brands'))){
            $brandsArray=explode(',' ,$request->get('brands'));
            $products= $products->whereIn('brand_id', $brandsArray);
        }
        
        if($request->get('price_max')!='' && $request->get('price_min')!=''){
            if($request->get('price_max')==50000){
            $products= $products->whereBetween('price', [intval($request->get('price_min')),1000000 ]);
            }
            else
            {
                $products= $products->whereBetween('price', [intval($request->get('price_min')),intval($request->get('price_max'))]);

            }
        }
       
        if($request->get('sort')!=''){
            if($request->get('sort')=='latest'){
                $products= $products->orderBy('id','DESC');
            }
            else if($request->get('sort')=='price_asc')
            {
             $products= $products->orderBy('price','ASC');
            }
            else{
                $products= $products->orderBy('price','DESC');
            }
        }
        else{
            $products= $products->orderBy('id','DESC');
        }
       
        $products= $products->paginate(9);

        $data['categories']=$categories;
        $data['brands']=$brands;
        $data['products']=$products;
        $data['categorySelected']=$categorySelected;
        $data['subcategorySelected']=$subcategorySelected;
        $data['brandsArray']=$brandsArray;
        $data['priceMax']=(intval($request->get('price_max'))==0)? 1000: $request->get('price_max');
        $data['priceMin']=intval($request->get('price_min'));
        $data['sort']=$request->get('sort');

        return view ('front.shop',$data);
    }


    public function product($slug){
        $products=Product::where('slug',$slug)->with('product_images')->first();

        if($products==null){
            abort(404);
        }

        $relatedProducts=[];
        // fetch related products
        if($products->related_product != ''){
            $productArray=explode(',',$products->related_product);
            $relatedProducts=Product::whereIn('id',$productArray)->get();
        }

        $data['relatedProducts']=$relatedProducts;
         
        $data['products']=$products;
        return view('front.product', $data);
    }
}
