@extends('admin.layout.app')

@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Product</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('products.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <form action="" method="post" id="productForm" name="productForm">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title">Title</label>
                                   <input type="text" name="title" id="title" class="form-control" placeholder="title" value="{{$products->title}}">
                                    <p class="error"></p>	
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="slug" value="{{$products->slug}}">	
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description">{{$products->description}}</textarea>
                                </div>
                            </div>      
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="sort_description"> Sort Description</label>
                                    <textarea name="sort_description" id="sort_description" cols="30" rows="5" class="summernote" placeholder="">{{$products->sort_description}}</textarea>
                                </div>
                            </div>       
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="shipping_return">Shipping and Return</label>
                                    <textarea name="shipping_return" id="shipping_return" cols="30" rows="5" class="summernote" placeholder="">{{$products->shipping_return}}</textarea>
                                </div>
                            </div>                                             
                        </div>
                    </div>	                                                                      
                </div>
              

                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Pricing</h2>								
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="price">Price</label>
                                    <input type="text" name="price" id="price" class="form-control" placeholder="Price" value="{{$products->price}}">	
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="compare_price">Compare at Price</label>
                                    <input type="text" name="compare_price" id="compare_price" class="form-control" placeholder="Compare Price" value="{{$products->compare_price}}">
                                    <p class="text-muted mt-3">
                                        To show a reduced price, move the product’s original price into Compare at price. Enter a lower value into Price.
                                    </p>	
                                </div>
                            </div>                                            
                        </div>
                    </div>	                                                                      
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Inventory</h2>								
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku">SKU (Stock Keeping Unit)</label>
                                    <input type="text" name="sku" id="sku" class="form-control" placeholder="sku" value="{{$products->sku}}">	
                                    <p class="error"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="barcode">Barcode</label>
                                    <input type="text" name="barcode" id="barcode" class="form-control" placeholder="Barcode" value="{{$products->barcode}}">	
                                </div>
                            </div>   
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="custom-control custom-checkbox">
                                        <input type="hidden" name="track_qty" value="No">
                                        <input class="custom-control-input" type="checkbox" id="track_qty" name="track_qty" value="Yes" {{($products->track_qty=='Yes')?'checked':''}}>
                                        <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <input type="number" min="0" name="qty" id="qty" class="form-control" placeholder="Qty" value="{{$products->qty}}">	
                                    <p class="error"></p>
                                </div>
                            </div>                                         
                        </div>
                    </div>	                                                                      
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Product status</h2>
                        <div class="mb-3">
                            <select name="status" id="status" class="form-control">
                                <option {{($products->status==1)?'selected':''}} value="1">Active</option>
                                <option {{($products->status==0)?'selected':''}} value="0">Block</option>
                            </select>
                        </div>
                    </div>
                </div> 

                <div class="card mb-3">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Media</h2>								
                        <div id="image" class="dropzone dz-clickable">
                            <div class="dz-message needsclick">    
                                <br>Drop files here or click to upload.<br><br>                                            
                            </div>
                        </div>
                    </div>	                                                                      
                </div>

                <div class="row" id="product-gallery">
                  @if($productImages->isNotEmpty())
                  @foreach($productImages as $productImage)

                  <div class="col-md-3" id="image-row-{{ $productImage->id}}">
                    <div class="card" >
                    <input type=hidden name="image_array[]" value="{{ $productImage->id}}">
                   <img src="{{asset('uploads/product/small/'. $productImage->image)}}" class="card-img-top" alt="">
                   <div class="card-body">
                   <a href="javascript:void(0)" onclick="deleteImage({{ $productImage->id}})" class="btn btn-danger">Delete</a>
                  </div>
                 </div>
                </div>

                  @endforeach
                  @endif
                </div>

                <div class="card">
                    <div class="card-body">	
                        <h2 class="h4  mb-3">Product category</h2>
                        <div class="mb-3">
                            <label for="category">Category</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">Select a Category</option>
                               @if($categories->isNotEmpty())
                               @foreach($categories as $category)
                               <option {{($products->category_id==$category->id)?'selected':''}} value="{{$category->id}}">{{$category->name}}</option>
                               @endforeach
                               @endif
                            </select>
                            <p class="error"></p>
                        </div>
                        <div class="mb-3">
                            <label for="sub_category">Sub category</label>
                            <select name="sub_category" id="sub_category" class="form-control">
                                <option value="">Select a Sub Category</option>
                                @if($subCategories->isNotEmpty())
                                @foreach($subCategories as $subCategory)
                                <option {{($products->sub_category_id==$subCategory->id)?'selected':''}} value="{{$subCategory->id}}">{{$subCategory->name}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Product brand</h2>
                        <div class="mb-3">
                            <select name="brand" id="brand" class="form-control">
                                <option value="">Select a brands</option>
                                @if($brands->isNotEmpty())
                               @foreach($brands as $brand)
                               <option  {{($products->brand_id==$brand->id)?'selected':''}} value="{{$brand->id}}">{{$brand->name}}</option>
                               @endforeach
                               @endif
                            </select>
                        </div>
                    </div>
                </div> 

                <div class="card">
                    <div class="card-body">	
                        <h2 class="h4  mb-3">Color & Size Variant</h2>
                        <div class="mb-3">
                            <label for="color">Color</label>
                            <select name="color" id="color" class="form-control">
                                <option value="">Select a Color</option>
                               @if($colors->isNotEmpty())
                               @foreach($colors as $color)
                               <option  {{($products->color_id==$color->id)?'selected':''}} value="{{$color->id}}">{{$color->name}}</option>
                               @endforeach
                               @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="size">Size</label>
                            <select name="size" id="size" class="form-control">
                                <option value="">select a size</option>
                                @if($sizes->isNotEmpty())
                                @foreach($sizes as $size)
                                <option  {{($products->size_id==$size->id)?'selected':''}} value="{{$size->id}}">{{$size->sortname}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div> 

                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Featured product</h2>
                        <div class="mb-3">
                            <select name="is_featured" id="is_featured" class="form-control">
                                <option {{($products->is_featured=='No')? 'selected':''}} value="No">No</option>
                                <option {{($products->is_featured=='Yes')? 'selected':''}} value="Yes">Yes</option>                                                
                            </select>
                            <p class="error"></p>
                        </div>
                    </div>
                </div>   
                
                <div class="card mb-3">
                    <div class="card-body">	
                        <h2 class="h4 mb-3">Related Product</h2>
                        <div class="mb-3">
                            <select multiple name="related_products[]" id="related_products" class="related-product w-100">
                                @if(!empty($relatedProducts))
                                @foreach ($relatedProducts as $relProducts )
                                    <option selected value="{{$relProducts->id}}">{{$relProducts->title}}</option>
                                @endforeach    
                                @endif                                  
                            </select>
                            <p class="error"></p>
                        </div>
                    </div>
                </div>            

            </div>
        </div>
        
        <div class="pb-5 pt-3">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{route('products.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </div>
</form>
    <!-- /.card -->
</section>

@endsection()

@section('customJs')
<script>
  
  $('.related-product').select2({
    ajax: {
        url: '{{ route("products.getproduct") }}',
        dataType: 'json',
        tags: true,
        multiple: true,
        minimumInputLength: 3,
        processResults: function (data) {
            return {
                results: data.tags
            };
        }
    }
}); 


  $("#productForm").submit(function(event){
         event.preventDefault();
         var formArray =$(this).serializeArray();
          $("button[type=submit]").prop('disabled',true);
         $.ajax({
            url:'{{route("products.update", $products->id)}}',
            type:'put',
            data:formArray,
            dataType:'json',
            success:function(response){
                $("button[type=submit]").prop('disabled',false);
                
                if(response["status"]==true){
                    $(".error").removeClass('invalid-feedback').html('');
                    $("input[type='text'],input[type='number'], select").removeClass('is-invalid');
                      window.location.href="{{route('products.index')}}";
                }
                
                else{
                    var errors = response['errors'];

                    $(".error").removeClass('invalid-feedback').html('');
                    $("input[type='text'],input[type='number'], select").removeClass('is-invalid');

                    $.each(errors,function(key,value){

                       $(`#${key}`).addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(value);
                    });

                }

            },
            error:function(){
                console.log("Something went wrong");
            }
         });
         
    });

    $("#category").change(function(){
        var category_id=$(this).val();
        $.ajax({
            url:'{{route('productsubcategories.index')}}',
            type:'get',
            data:{category_id:category_id},
            dataType:'json',
            success:function(response){
               
                $("#sub_category").find("option").not(":first").remove();
                $.each(response["subCategories"],function(key,item){
                    $("#sub_category").append(`<option value='${item.id}'>${item.name}</option>`)
                });
            },
            error : function(){
                console.log("Something went Wrong");
            }
        });
    });

       $("#title").change(function(){
        element=$(this);
        $("button[type=submit]").prop('disabled',true);
      
        $.ajax({
            url:'{{route("getslug")}}',
            type:'get',
            data: {title:element.val()},
            dataType:'json',
            success:function(response){
                $("button[type=submit]").prop('disabled',false);
            if(response["status"]==true){
                $("#slug").val(response["slug"]);

            }
        }
        });

    });

    Dropzone.autoDiscover = false;    
   const dropzone = $("#image").dropzone({ 
  
    url:  "{{ route('product-images.update') }}",
    maxFiles: 10,
    paramName: 'image',
    params:{'product_id':{{$products->id}}},
    addRemoveLinks: true,
    acceptedFiles: "image/jpeg,image/png,image/gif",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }, success: function(file, response){
        // $("#image_id").val(response.image_id);
        //console.log(response)

       var html= `<div class="col-md-3" id="image-row-${response.image_id}"><div class="card" >
        <input type=hidden name="image_array[]" value="${response.image_id}">
       <img src="${response.imagePath}" class="card-img-top" alt="">
       <div class="card-body">
       <a href="javascript:void(0)" onclick="deleteImage(${response.image_id})" class="btn btn-danger">Delete</a>
      </div>
     </div></div>`;
      $("#product-gallery").append(html);
    },
    complete:function(file){
        this.removeFile(file);
    }
});

function deleteImage(id){
    $("#image-row-"+id).remove();

    if(confirm("Are you sure you want to delete image "))
    {
    $.ajax({
        url:'{{route("product-images.destroy")}}',
        type:'delete',
        data:{id:id},
        success: function(response){
            if(response.status==true){
                alert(response.message);
            }
            else{
                alert(response.message);
            }

        }
    });
    }
}
</script>
@endsection()