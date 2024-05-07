@extends('admin.layout.app')

@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Shipping Managment</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="#" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="shippingForm" name="shippingForm">
        <div class="card">
            <div class="card-body">								
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                           <select name="country" id="country" class="form-control">
                            <option value="">select a country</option>
                            @if($countries->isNotEmpty())
                              @foreach($countries as $country)
                              <option value="{{$country->id}}">{{$country->name}}</option>
                              @endforeach
                                    <option value="Rest_of_world">Rest Of World</option>
                            @endif
                        </select>	
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <input type="amount" name="amount" id="amount" class="form-control" placeholder="Amount">
                            <p></p>	
                        </div>
                </div>
            </div>							
        </div>
        <div class="pb-3 pt-2 pl-3">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="#" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </form>
    </div>
    <!-- /.card -->
</section>

@endsection

@section('customJs')

<script>
    $("#shippingForm").submit(function(event){
         event.preventDefault();
          var element=$(this);
          $("button[type=submit]").prop('disabled',true);
         $.ajax({
            url:'{{route("categories.store")}}',
            type:'post',
            data: element.serializeArray(),
            dataType:'json',
            success:function(response){
                $("button[type=submit]").prop('disabled',false);
                
                if(response["status"]==true){

                    window.location.href="{{route('categories.index')}}";

                    $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                    $("#slug").removeClass("is-invalid")
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                }else{
                    var errors = response['errors'];
                   if(errors['name']) {
                    $("#name").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback').html(errors['name']);
                    }
                   else{
                    $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                   }

                   if(errors['slug']) {
                    $("#slug").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback').html(errors['slug']);
                }
                else{
                    $("#slug").removeClass("is-invalid")
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                }

                }

            },
            error:function(jqXHR,exception){
                console.log("Something went wrong");
            }
         });
         
    });

    
</script>
@endsection

