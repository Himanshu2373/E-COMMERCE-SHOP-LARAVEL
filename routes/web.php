<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\TempImagesController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductSubCategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\AuthController;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('/cart-update',[CartController::class,'cartUpdate'])->name('front.cartUpdate');
Route::post('/cart-delete',[CartController::class,'cartDelete'])->name('front.delete');
Route::get('/checkout',[CartController::class,'checkout'])->name('front.checkout');
Route::post('/process-checkout',[CartController::class,'processcheckout'])->name('front.processcheckout');
Route::get('/thankyou/{orderId}',[CartController::class,'thankyou'])->name('front.thankyou');

// Route::get('/login',[AuthController::class,'login'])->name('account.login');
// Route::get('/register',[AuthController::class,'register'])->name('account.register');
// Route::post('/process-register',[AuthController::class,'processRegister'])->name('account.processRegister');

Route::group(['prefix'=>'accounts'],function(){
    Route::group(['middleware'=>'guest'],function(){

        Route::get('/login',[AuthController::class,'login'])->name('account.login');
        Route::post('/login',[AuthController::class,'authenticate'])->name('account.authenticate');

        Route::get('/register',[AuthController::class,'register'])->name('account.register');
        Route::post('/process-register',[AuthController::class,'processRegister'])->name('account.processRegister');

       

    });

    Route::group(['middleware'=>'auth'],function(){
        Route::get('/profile',[AuthController::class,'profile'])->name('account.profile');
        Route::get('/logout',[AuthController::class,'logout'])->name('account.logout');
        

    });
});

Route::get('/admin/login',[AdminLogController::class,'index'])->name('admin.login');

Route::group(['prefix'=>'admin'],function(){
    Route::group(['middleware'=>'admin.guest'],function(){

        Route::get('/login',[AdminLogController::class,'index'])->name('admin.login');
        Route::post('/authenticate',[AdminLogController::class,'authenticate'])->name('admin.authenticate');

    });

    Route::group(['middleware'=>'admin.auth'],function(){
        Route::get('/dashboard',[DashboardController::class,'index'])->name('admin.dashboard');
        Route::get('/logout',[DashboardController::class,'logout'])->name('admin.logout');

        // category routes
        Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories',[CategoryController::class,'store'])->name('categories.store');
        Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');
        Route::put('/categories/{category}',[CategoryController::class,'update'])->name('categories.update');
        Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->name('categories.delete');

        // Sub Categories
        Route::get('/sub-categories',[SubCategoryController::class,'index'])->name('sub-categories.index');
        Route::get('/sub-categories/create',[SubCategoryController::class,'create'])->name('sub-categories.create');
        Route::post('/sub-categories',[SubCategoryController::class,'store'])->name('sub-categories.store');
        Route::get('/sub-categories/{subCategory}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subCategory}',[SubCategoryController::class,'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{subCategory}',[SubCategoryController::class,'destroy'])->name('sub-categories.delete');

        // Brand route
        Route::get('/brands',[BrandController::class,'index'])->name('brands.index');
        Route::get('/brands/create',[BrandController::class,'create'])->name('brands.create');
        Route::post('/brands',[BrandController::class,'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit',[BrandController::class,'edit'])->name('brands.edit');
        Route::put('/brands/{brand}',[BrandController::class,'update'])->name('brands.update');
        Route::delete('/brands/{brand}',[BrandController::class,'destroy'])->name('brands.delete');

       // product route
       Route::get('/products',[ProductController::class,'index'])->name('products.index');
       Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
       Route::post('/products',[ProductController::class,'store'])->name('products.store');
       Route::get('/products/{product}/edit',[ProductController::class,'edit'])->name('products.edit');
       Route::put('/products/{product}',[ProductController::class,'update'])->name('products.update');
       Route::delete('/products/{product}',[ProductController::class,'destroy'])->name('products.delete');

       Route::get('/get-products',[ProductController::class,'getproduct'])->name('products.getproduct');
       // product subcategories
       Route::get('/productsubcategories',[ProductSubCategoryController::class,'index'])->name('productsubcategories.index');
        // product image route
       Route::post('/product-images/update',[ProductImageController::class,'update'])->name('product-images.update');
       Route::delete('/product-images',[ProductImageController::class,'destroy'])->name('product-images.destroy');



      //  color route
       Route::get('/color',[ColorController::class,'index'])->name('color.index');
       Route::get('/color/create',[ColorController::class,'create'])->name('color.create');
       Route::post('/color',[ColorController::class,'store'])->name('color.store');
       Route::get('/color/{color}/edit',[ColorController::class,'edit'])->name('color.edit');
       Route::put('/color/{color}',[ColorController::class,'update'])->name('color.update');
       Route::delete('/color/{color}',[ColorController::class,'destroy'])->name('color.delete');

       //  Size route
       Route::get('/size',[SizeController::class,'index'])->name('size.index');
       Route::get('/size/create',[SizeController::class,'create'])->name('size.create');
       Route::post('/size',[SizeController::class,'store'])->name('size.store');
       Route::get('/size/{size}/edit',[SizeController::class,'edit'])->name('size.edit');
       Route::put('/size/{size}',[SizeController::class,'update'])->name('size.update');
       Route::delete('/size/{size}',[SizeController::class,'destroy'])->name('size.delete');
     
       //shipping route
       Route::get('/shipping/create',[ShippingController::class,'create'])->name('shipping.create');
       Route::post('/shipping',[ShippingController::class,'store'])->name('shipping.store');
       Route::get('/shipping/{id}',[ShippingController::class,'edit'])->name('shipping.edit');
       Route::put('/shipping/{id}',[ShippingController::class,'update'])->name('shipping.update');
       Route::delete('/shipping/{id}',[ShippingController::class,'destroy'])->name('shipping.delete');



       //    temp-image.create
        Route::post('/upload-temp-image',[TempImagesController::class,'create'])->name('temp-images.create');


        Route::get('/getslug',function(Request $request){
            $slug='';
            if(!empty($request->title)){
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status'=>true,
                'slug'=>$slug
            ]);
        })->name('getslug');
    });

});